<?php
/**
 * (C) Copyright 2017 Nuxeo SA (http://nuxeo.com/) and contributors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Nuxeo\Client\Internals\Spi\Objects;


use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Plugin\Log\LogPlugin;
use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\NuxeoClient;
use Nuxeo\Client\Api\Objects\Blob\Blob;
use Nuxeo\Client\Api\Request;
use Nuxeo\Client\Internals\Spi\Annotations\HttpMethod;
use Nuxeo\Client\Internals\Spi\ClassCastException;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;
use Nuxeo\Client\Internals\Spi\NuxeoException;
use Nuxeo\Client\Internals\Util\HttpUtils;
use Zend\Uri\Http as HttpUri;
use Zend\Uri\Uri;

abstract class NuxeoEntity {

  const className = __CLASS__;

  /**
   * @Serializer\SerializedName("entity-type")
   * @Serializer\Type("string")
   */
  private $entityType;

  /**
   * @var NuxeoClient
   * @Serializer\Exclude()
   */
  private $nuxeoClient;

  /**
   * @var string
   * @Serializer\SerializedName("repository")
   * @Serializer\Type("string")
   */
  private $repositoryName;

  /**
   * NuxeoEntity constructor.
   * @param $entityType
   * @param NuxeoClient $nuxeoClient
   */
  public function __construct($entityType, $nuxeoClient = null) {
    $this->entityType = $entityType;
    $this->nuxeoClient = $nuxeoClient;
  }

  /**
   * @param string $path
   * @return Uri
   */
  protected function computeRequestUrl($path) {
    return HttpUri::merge($this->getNuxeoClient()->getApiUrl(), $path);
  }

  protected function getCall() {
    $backtrace = debug_backtrace();
    $reflectionClass = new \ReflectionClass($backtrace[2]['class']);
    $reflectionMethod = $reflectionClass->getMethod($backtrace[2]['function']);

    $params = array();
    $paramIndex = 0;
    $paramValues = $backtrace[2]['args'];
    $paramNames = array_map(function ($parameter) {
      /** @var \ReflectionParameter $parameter */
      return array($parameter->name, $parameter->isDefaultValueAvailable()?$parameter->getDefaultValue():null);
    }, $reflectionMethod->getParameters());

    foreach($paramNames as $param) {
      list($name, $default) = $param;
      $params[$name] = isset($paramValues[$paramIndex])?$paramValues[$paramIndex]:$default;
      $paramIndex++;
    }

    return array($reflectionMethod, $params);
  }

  /**
   * @param string $type
   * @param string $body
   * @param array $files
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  protected function getResponse($type = null, $body = null, $files = array()) {
    list($reflectionMethod, $params) = $this->getCall();
    $request = $this->getRequest($reflectionMethod, $params);

    if(is_array($files)) {
      foreach($files as $file) {
        $request->addRelatedFile($file);
      }
    }

    if(null !== $body) {
      if(!is_string($body)) {
        $body = $this->nuxeoClient->getConverter()->writeJSON($body);
      }
      $request->setBody($body);
    }

    try {
      $response = $this->getNuxeoClient()->perform($request);

      if($response->getBody()->getContentLength() > 0) {
        if(false === (
            HttpUtils::isContentType($response, Constants::CONTENT_TYPE_JSON) ||
            HttpUtils::isContentType($response, Constants::CONTENT_TYPE_JSON_NXENTITY))) {

          if(Blob::className !== $type) {
            throw new ClassCastException(sprintf('Cannot cast %s as %s', Blob::className, $type));
          }

          return Blob::fromHttpResponse($response);
        }
        $body = $this->nuxeoClient->getConverter()->readJSON($response->getBody(true), $type);

        return $this->reconnectObject($body, $this->getNuxeoClient());
      }
    } catch(BadResponseException $e) {
      $response = $e->getResponse();
      $responseBody = $response->getBody(true);
      if(empty($responseBody)) {
        throw new NuxeoClientException($response->getReasonPhrase(), $response->getStatusCode());
      } elseif(!HttpUtils::isContentType($response, Constants::CONTENT_TYPE_JSON)) {
        throw new NuxeoClientException($responseBody, $response->getStatusCode());
      } else {
        throw NuxeoClientException::fromPrevious(
          $this->getNuxeoClient()->getConverter()->readJSON($responseBody, NuxeoException::className),
          $response->getReasonPhrase(),
          $response->getStatusCode()
        );
      }
    }
    return null;
  }

  /**
   * @throws NuxeoClientException
   * @return Request
   */
  protected function getRequest($reflectionMethod, $params) {
    foreach($this->getNuxeoClient()->getAnnotationReader()->getMethodAnnotations($reflectionMethod) as $annotation) {
      if($annotation instanceof HttpMethod) {
        try {
          $request = new Request(
            $annotation->getName(),
            $this->computeRequestUrl($annotation->computePath($params))
          );
          if($this->getNuxeoClient()->isDebug()) {
            $request->addSubscriber(LogPlugin::getDebugPlugin(true, $this->getNuxeoClient()->getDebugStream()));
          }
          return $request;
        } catch(\InvalidArgumentException $e) {
          throw NuxeoClientException::fromPrevious($e);
        }
      }
    }
    throw new NuxeoClientException(
      sprintf('No method found for method name "%s". Check method name and parameters.',
        $reflectionMethod->getName())
    );
  }

  /**
   * @param $object
   * @param $nuxeoClient
   * @return mixed
   */
  protected function reconnectObject($object, $nuxeoClient) {
    if($object instanceof NuxeoEntity) {
      $object->nuxeoClient = $nuxeoClient;
    }
    return $object;
  }

  /**
   * @return NuxeoClient
   */
  protected function getNuxeoClient() {
    return $this->nuxeoClient;
  }

  /**
   * @return string
   */
  public function getEntityType() {
    return $this->entityType;
  }

  /**
   * @return string
   */
  public function getRepositoryName() {
    return $this->repositoryName;
  }

}

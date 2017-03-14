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


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\NuxeoClient;
use Nuxeo\Client\Api\Objects\Blob\Blob;
use Nuxeo\Client\Api\Request;
use Nuxeo\Client\Internals\Spi\Annotations\HttpMethod;
use Nuxeo\Client\Internals\Spi\ClassCastException;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;
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

  /**
   * @param string $type
   * @param string $body
   * @param array $files
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  protected function getResponse($type = null, $body = null, $files = null) {
    $request = $this->getRequest();

    if(is_array($files)) {
      foreach($files as $file) {
        $request->addRelatedFile($file);
      }
    }

    if(null !== $body) {
      $request->setBody($body);
    }

    $response = $this->getNuxeoClient()->perform($request);

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

  /**
   * @throws NuxeoClientException
   * @return Request
   */
  protected function getRequest() {
    $backtrace = debug_backtrace();
    $reflectionClass = new \ReflectionClass($backtrace[2]['class']);
    $reflectionMethod = $reflectionClass->getMethod($backtrace[2]['function']);

    foreach($this->getNuxeoClient()->getAnnotationReader()->getMethodAnnotations($reflectionMethod) as $annotation) {
      if($annotation instanceof HttpMethod) {
        try {
          $params = array();
          $paramIndex = 0;
          $paramValues = $backtrace[2]['args'];
          $paramNames = array_map(function ($parameter) {
            /** @var \ReflectionParameter $parameter */
            return $parameter->name;
          }, $reflectionMethod->getParameters());

          foreach($paramNames as $name) {
            $params[$name] = isset($paramValues[$paramIndex])?$paramValues[$paramIndex]:null;
            $paramIndex++;
          }

          return new Request(
            $annotation->getName(),
            $this->computeRequestUrl($annotation->computePath($params))
          );
        } catch(\InvalidArgumentException $e) {
          throw NuxeoClientException::fromPrevious($e);
        }
      }
    }
    throw new NuxeoClientException(
      sprintf('No method found for API %s and method name "%s". Check method name and parameters.',
        $reflectionClass->getName(),
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

}

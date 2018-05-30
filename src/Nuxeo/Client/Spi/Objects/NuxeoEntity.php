<?php
/**
 * (C) Copyright 2018 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 */

namespace Nuxeo\Client\Spi\Objects;


use function \is_array, \is_string;
use Doctrine\Common\Annotations\AnnotationException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use function GuzzleHttp\Psr7\stream_for;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Constants;
use Nuxeo\Client\NuxeoClient;
use Nuxeo\Client\Objects\Blob\Blob;
use Nuxeo\Client\Request;
use Nuxeo\Client\Spi\ClassCastException;
use Nuxeo\Client\Spi\Http\Method\AbstractMethod;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\NuxeoException;
use Nuxeo\Client\Util\HttpUtils;
use Psr\Http\Message\UriInterface;

abstract class NuxeoEntity {

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
   * @return UriInterface
   */
  protected function computeRequestUrl($path) {
    return UriResolver::resolve($this->getNuxeoClient()->getApiUrl(), new Uri($path));
  }

  /**
   * @return array
   * @throws \ReflectionException
   */
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

    foreach($paramNames as [$name, $default]) {
      $params[$name] = $paramValues[$paramIndex] ?? $default;
      $paramIndex++;
    }

    return array($reflectionMethod, $params);
  }

  /**
   * @param AbstractMethod $method
   * @param null $type
   * @return null
   * @throws \Nuxeo\Client\Spi\NuxeoClientException
   * @throws \Nuxeo\Client\Spi\ClassCastException
   */
  protected function getResponseNew(AbstractMethod $method, $type = null) {
    $body = $method->getBody();
    $files = $method->getFiles();

    try {
      [, $params] = $this->getCall();
    } catch(\ReflectionException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }

    $request = $this->getRequest($method, $params);

    if(is_array($files)) {
      foreach($files as $file) {
        $request = $request->addRelatedFile($file);
      }
    }

    try {
      if(null !== $body) {
        if(!is_string($body)) {
          $body = $this->nuxeoClient->getConverter()->writeJSON($body);
        }
        $request = $request->withBody(stream_for($body));
      }

      $response = $this->getNuxeoClient()->perform($request);

      if($response->getBody()->getSize() > 0) {
        if(false === (
            HttpUtils::isContentType($response, Constants::CONTENT_TYPE_JSON) ||
            HttpUtils::isContentType($response, Constants::CONTENT_TYPE_JSON_NXENTITY))) {

          if(Blob::class !== $type) {
            throw new ClassCastException(sprintf('Cannot cast %s as %s', Blob::class, $type));
          }

          return Blob::fromHttpResponse($response);
        }
        $body = $this->nuxeoClient->getConverter()->readJSON((string) $response->getBody(), $type);

        return $this->reconnectObject($body, $this->getNuxeoClient());
      }
    } catch(BadResponseException $e) {
      $response = $e->getResponse();
      $responseBody = (string) $response->getBody();
      if(empty($responseBody)) {
        throw new NuxeoClientException($response->getReasonPhrase(), $response->getStatusCode());
      }

      if(!HttpUtils::isContentType($response, Constants::CONTENT_TYPE_JSON)) {
        throw new NuxeoClientException($responseBody, $response->getStatusCode());
      }

      try {
        throw NuxeoClientException::fromPrevious(
          $this->getNuxeoClient()->getConverter()->readJSON($responseBody, NuxeoException::class),
          $response->getReasonPhrase(),
          $response->getStatusCode()
        );
      } catch(AnnotationException $e) {
        throw new NuxeoClientException($responseBody, $response->getStatusCode());
      }
    } catch(GuzzleException|AnnotationException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }
    return null;
  }

  /**
   * @param AbstractMethod $method
   * @param $params
   * @throws NuxeoClientException
   * @return Request
   */
  protected function getRequest(AbstractMethod $method, $params) {
    try {
      $request = $this->getNuxeoClient()->createRequest(
        $method->getName(),
        $this->computeRequestUrl($method->computePath($params))
      );

      return $request;
    } catch(\InvalidArgumentException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }
  }

  /**
   * @param $object
   * @param $nuxeoClient
   * @return mixed
   */
  protected function reconnectObject($object, $nuxeoClient) {
    if($object instanceof self) {
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

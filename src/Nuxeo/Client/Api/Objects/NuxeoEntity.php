<?php
/**
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 * Contributors:
 *     Pierre-Gildas MILLON <pgmillon@nuxeo.com>
 */

namespace Nuxeo\Client\Api\Objects;


use Guzzle\Http\Message\Response;
use Guzzle\Http\Url;
use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\NuxeoClient;
use Nuxeo\Client\Api\Objects\Blob\Blob;
use Nuxeo\Client\Internals\Spi\Annotations\HttpMethod;
use Nuxeo\Client\Internals\Spi\ClassCastException;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;

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
   * @return Url
   */
  protected function computeRequestUrl($path) {
    return $this->getNuxeoClient()->getApiUrl()->addPath($path);
  }

  /**
   * @param Response $response
   * @param string $type
   * @return mixed
   * @throws ClassCastException
   */
  protected function computeResponse($response, $type = null) {
    if(false === (
        $response->isContentType(Constants::CONTENT_TYPE_JSON) ||
        $response->isContentType(Constants::CONTENT_TYPE_JSON_NXENTITY))) {

      if(Blob::className !== $type) {
        throw new ClassCastException(sprintf('Cannot cast %s as %s', Blob::className, $type));
      }

      return Blob::fromHttpResponse($response);
    }
    $body = $this->nuxeoClient->getConverter()->readJSON($response->getBody(true), $type);

    return $this->reconnectObject($body, $this->getNuxeoClient());
  }

  /**
   * @param string $type
   * @return mixed
   * @throws ClassCastException
   */
  public function getResponse($type = null, $body = null, $files = null) {
    //TODO: Use Zend\Http\Query
    $method = $this->getMethod();
    $args = array();

    $response = $this->getNuxeoClient()->{$method->getName()}(
      $this->computeRequestUrl($method->getPath()),
      $body,
      $files
    );
    if(false === (
        $response->isContentType(Constants::CONTENT_TYPE_JSON) ||
        $response->isContentType(Constants::CONTENT_TYPE_JSON_NXENTITY))) {

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
   * @return HttpMethod
   */
  protected function getMethod() {
    $backtrace = debug_backtrace();
    $reflectionClass = new \ReflectionClass($backtrace[2]['class']);
    $reflectionMethod = $reflectionClass->getMethod($backtrace[2]['function']);

    foreach($this->getNuxeoClient()->getAnnotationReader()->getMethodAnnotations($reflectionMethod) as $annotation) {
      if($annotation instanceof HttpMethod) {
        return $annotation;
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

}

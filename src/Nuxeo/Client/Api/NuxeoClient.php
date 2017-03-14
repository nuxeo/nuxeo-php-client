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
 */

namespace Nuxeo\Client\Api;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Nuxeo\Client\Api\Auth\BasicAuthentication;
use Nuxeo\Client\Api\Marshaller;
use Nuxeo\Client\Api\Objects\Blob\Blob;
use Nuxeo\Client\Api\Objects\Blob\Blobs;
use Nuxeo\Client\Api\Objects\Operation;
use Nuxeo\Client\Api\Objects\Repository;
use Nuxeo\Client\Internals\Spi\Auth\AuthenticationInterceptor;
use Nuxeo\Client\Internals\Spi\Http\Client;
use Nuxeo\Client\Internals\Spi\Interceptor;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;
use Nuxeo\Client\Internals\Spi\SimpleInterceptor;
use Zend\Uri\Exception\InvalidUriPartException;
use Zend\Uri\Http as HttpUri;

AnnotationRegistry::registerLoader('class_exists');

/**
 * Class NuxeoClient
 * @package Nuxeo\Client
 */
class NuxeoClient {

  /**
   * @var HttpUri
   */
  private $baseUrl;

  /**
   * @var Interceptor[]
   */
  private $interceptors = array();

  /**
   * @var Client
   */
  private $httpClient;

  /**
   * @var Marshaller\NuxeoConverter
   */
  private $converter;

  /**
   * @var Reader
   */
  private $annotationReader;

  /**
   * @param string $url
   * @param string $username
   * @param string $password
   * @throws NuxeoClientException
   */
  public function __construct($url = 'http://localhost:8080/nuxeo', $username = 'Administrator', $password = 'Administrator') {
    try {
      $this->setBaseUrl($url);
    } catch(\InvalidArgumentException $e) {
      NuxeoClientException::fromPrevious($e);
    }

    $this->setAuthenticationMethod(new BasicAuthentication($username, $password));
  }

  /**
   * @param string|\Zend\Uri\Uri $baseUrl
   * @throws \InvalidArgumentException
   */
  public function setBaseUrl($baseUrl) {
    if (is_string($baseUrl)) {
      try {
        $baseUrl = new HttpUri($baseUrl);
      } catch (InvalidUriPartException $e) {
        throw new \InvalidArgumentException(
          sprintf('Invalid URI passed as string (%s)', (string) $baseUrl),
          $e->getCode(),
          $e
        );
      }
    } elseif (!($baseUrl instanceof HttpUri)) {
      throw new \InvalidArgumentException(
        'URI must be an instance of Zend\Uri\Http or a string'
      );
    }
    $this->baseUrl = $baseUrl;
  }

  /**
   * @return \Zend\Uri\Uri
   */
  public function getBaseUrl() {
    return $this->baseUrl;
  }

  /**
   * @return \Zend\Uri\Uri
   */
  public function getApiUrl() {
    return HttpUri::merge($this->getBaseUrl(), Constants::API_PATH);
  }

  /**
   * @param string ...
   * @return NuxeoClient
   */
  public function schemas() {
    $this->header(Constants::HEADER_PROPERTIES, implode(',', func_get_args()));
    return $this;
  }

  /**
   * @param boolean $value
   * @return NuxeoClient
   */
  public function voidOperation($value) {
    $this->header(Constants::HEADER_VOID_OPERATION, $value ? 'true' : 'false');
    return $this;
  }

  /**
   * @param string $outputFile
   * @return NuxeoClient
   */
  public function debug($outputFile = null) {
    //TODO: Fixme
//    $stream = $outputFile ? fopen($outputFile, 'w+b') : null;
//    $this->httpClient->addSubscriber(LogPlugin::getDebugPlugin(true, $stream));
    return $this;
  }

  /**
   * @see http://explorer.nuxeo.com/nuxeo/site/distribution/current/listOperations List of available operations
   * @param string $operationId
   * @return Operation
   */
  public function automation($operationId = null) {
    return new Operation($this, $operationId);
  }

  public function repository() {
    return new Repository($this);
  }

  /**
   * @param AuthenticationInterceptor $authenticationInterceptor
   * @return NuxeoClient
   */
  public function setAuthenticationMethod(AuthenticationInterceptor $authenticationInterceptor) {
    foreach($this->interceptors as $i => $interceptor) {
      if($interceptor instanceof AuthenticationInterceptor) {
        unset($this->interceptors[$i]);
      }
    }
    $this->interceptors[] = $authenticationInterceptor;
    return $this;
  }

  /**
   * @param string $name
   * @param string $value
   * @return NuxeoClient
   */
  public function header($name, $value) {
    $self = $this;

    $this->interceptors[] = new SimpleInterceptor(
      function(Request $request) use ($self, $name, $value) {
        $request->addHeader($name, $value);
      }
    );

    return $this;
  }

  /**
   * @param string $url
   * @param array $query
   * @return Response
   * @throws NuxeoClientException
   */
  public function get($url, $query = array()) {
    $request = new Request(Request::GET, $url);
    $response = null;

    try {
      $request->getQuery()->replace($query);

      $this->interceptors($request);

      $response = $this->getHttpClient()->send($request);

      if($response->getStatusCode() >= 400) {
        throw new NuxeoClientException($response->getBody(true));
      }
    } catch(\RuntimeException $ex) {
      throw NuxeoClientException::fromPrevious($ex);
    } catch(\InvalidArgumentException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }

    return $response;
  }

  /**
   * @param string $url
   * @param string $body
   * @param array $files
   * @return Response
   * @throws NuxeoClientException
   */
  public function post($url, $body = null, $files = array()) {
    $request = new Request(Request::POST, $url);

    try {
      $request->setBody($body);

      foreach($files as $file) {
        $request->addRelatedFile($file);
      }

      $this->interceptors($request);
      return $this->getHttpClient()->send($request);
    } catch(\RuntimeException $ex) {
      throw NuxeoClientException::fromPrevious($ex);
    } catch(\InvalidArgumentException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }
  }

  /**
   * @param $request
   * @return Response
   * @throws NuxeoClientException
   */
  public function perform($request) {
    $this->interceptors($request);
    return $this->getHttpClient()->send($request);
  }

  /**
   * @return Marshaller\NuxeoConverter
   */
  public function getConverter() {
    if(null === $this->converter) {
      $this->converter = new Marshaller\NuxeoConverter($this->getAnnotationReader());
      $this->setupDefaultMarshallers();
    }
    return $this->converter;
  }

  /**
   * @param string $applicationName
   * @param string $deviceId
   * @param string $deviceDescription
   * @param string $permission
   * @param boolean $revoke
   * @return string
   * @throws NuxeoClientException
   */
  public function requestAuthenticationToken($applicationName, $deviceId, $deviceDescription = '', $permission = Constants::SECURITY_READ_WRITE, $revoke = false) {
    $res = $this->get('authentication/token', array(
      'applicationName' => $applicationName,
      'deviceId' => $deviceId,
      'deviceDescription' => $deviceDescription,
      'permission' => $permission, 'revoke' => $revoke
    ), array('allow_redirects' => false));
    if ($res->getStatusCode() > 205) {
      throw new NuxeoClientException($res->getStatusCode());
    }
    return $res->getBody();
  }

  /**
   * @return NuxeoClient
   */
  protected function setupDefaultMarshallers() {
    $this->getConverter()->registerMarshaller(Blob::className, new Marshaller\BlobMarshaller());
    $this->getConverter()->registerMarshaller(Blobs::className, new Marshaller\BlobsMarshaller());
    $this->getConverter()->registerMarshaller(Operation\ActionList::className, new Marshaller\ActionListMarshaller());
    $this->getConverter()->registerMarshaller(Operation\CounterList::className, new Marshaller\CounterListMarshaller());
    $this->getConverter()->registerMarshaller(Operation\CounterTimestampedValue::className, new Marshaller\CounterTimestampedValueMarshaller());
    $this->getConverter()->registerMarshaller(Operation\DirectoryEntries::className, new Marshaller\DirectoryEntriesMarshaller());
    $this->getConverter()->registerMarshaller(Operation\DocRef::className, new Marshaller\DocRefMarshaller($this));
    $this->getConverter()->registerMarshaller(Operation\LogEntries::className, new Marshaller\LogEntriesMarshaller());
    $this->getConverter()->registerMarshaller(Operation\UserGroupList::className, new Marshaller\UserGroupListMarshaller());
    return $this;
  }

  /**
   * @return Client
   * @throws NuxeoClientException
   */
  protected function getHttpClient() {
    if(null === $this->httpClient) {
      $this->httpClient = new Client();
    }
    return $this->httpClient;
  }

  /**
   * @param \Nuxeo\Client\Api\Request $request
   * @return NuxeoClient
   * @throws NuxeoClientException
   */
  protected function interceptors($request) {
    foreach($this->interceptors as $interceptor) {
      $interceptor->proceed($this->getHttpClient(), $request);
    }
    return $this;
  }

  /**
   * @return Reader
   */
  public function getAnnotationReader() {
    if(null === $this->annotationReader) {
      $this->annotationReader = new AnnotationReader();
    }
    return $this->annotationReader;
  }

}

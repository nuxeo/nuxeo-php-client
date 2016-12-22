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

namespace Nuxeo\Client\Api;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Guzzle\Common\Exception\GuzzleException;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Url;
use Guzzle\Plugin\Log\LogPlugin;
use Nuxeo\Client\Api\Auth\BasicAuthentication;
use Nuxeo\Client\Api\Marshaller\BlobMarshaller;
use Nuxeo\Client\Api\Marshaller\BlobsMarshaller;
use Nuxeo\Client\Api\Marshaller\DirectoryEntriesMarshaller;
use Nuxeo\Client\Api\Marshaller\NuxeoConverter;
use Nuxeo\Client\Api\Objects\Blob;
use Nuxeo\Client\Api\Objects\Blobs;
use Nuxeo\Client\Api\Objects\DirectoryEntries;
use Nuxeo\Client\Api\Objects\Operation;
use Nuxeo\Client\Internals\Spi\Auth\AuthenticationInterceptor;
use Nuxeo\Client\Internals\Spi\Http\EntityEnclosingRequest;
use Nuxeo\Client\Internals\Spi\Http\RequestFactory;
use Nuxeo\Client\Internals\Spi\Interceptor;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;
use Nuxeo\Client\Internals\Spi\SimpleInterceptor;

AnnotationRegistry::registerLoader('class_exists');

/**
 * Class NuxeoClient
 * @package Nuxeo\Client
 */
class NuxeoClient {

  /**
   * @var string
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
   * @var NuxeoConverter
   */
  private $converter;

  /**
   * @param string $url
   * @param string $username
   * @param string $password
   * @throws NuxeoClientException
   */
  public function __construct($url = 'http://localhost:8080/nuxeo', $username = 'Administrator', $password = 'Administrator') {
    try {
      $this->baseUrl = Url::factory($url);
      $this->httpClient = new Client($url, array(
        Client::REQUEST_OPTIONS => array(
          'headers' => array(
            'Content-Type' => 'application/json+nxrequest'
          )
        )
      ));
    } catch(GuzzleException $ex) {
      throw NuxeoClientException::fromPrevious($ex);
    }

    $this->httpClient->setRequestFactory(new RequestFactory());

    $this->setAuthenticationMethod(new BasicAuthentication($username, $password));

  }

  /**
   * @return Url
   */
  public function getBaseUrl() {
    return $this->baseUrl;
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
    $stream = $outputFile ? fopen($outputFile, 'w+b') : null;
    $this->httpClient->addSubscriber(LogPlugin::getDebugPlugin(true, $stream));
    return $this;
  }

  /**
   * @see http://explorer.nuxeo.com/nuxeo/site/distribution/current/listOperations List of available operations
   * @param string $operationId
   * @return Operation
   */
  public function automation($operationId = null) {
    $url = clone $this->baseUrl;
    return new Operation($this, $url->addPath(Constants::AUTOMATION_PATH), $operationId);
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
      function(RequestInterface $request) use ($self, $name, $value) {
        $request->addHeader($name, $value);
      }
    );

    return $this;
  }

  /**
   * @param string $url
   * @param string $body
   * @param array $files
   * @return Response
   * @throws NuxeoClientException
   */
  public function post($url, $body = null, $files = array()) {
    /** @var EntityEnclosingRequest $request */
    $request = $this->getHttpClient()->createRequest(Request::POST, $url, null, $body);

    foreach($files as $file) {
      $request->addRelatedFile($file);
    }

    $this->interceptors($request);
    try {
      return $request->send();
    } catch(GuzzleException $ex) {
      throw NuxeoClientException::fromPrevious($ex);
    }
  }

  /**
   * @return NuxeoConverter
   */
  public function getConverter() {
    if(null === $this->converter) {
      $this->converter = new NuxeoConverter();
      $this->setupDefaultMarshallers();
    }
    return $this->converter;
  }

  /**
   * @return NuxeoClient
   */
  protected function setupDefaultMarshallers() {
    $this->getConverter()->registerMarshaller(DirectoryEntries::className, new DirectoryEntriesMarshaller());
    $this->getConverter()->registerMarshaller(Blob::className, new BlobMarshaller());
    $this->getConverter()->registerMarshaller(Blobs::className, new BlobsMarshaller());
    return $this;
  }

  /**
   * @return Client
   */
  protected function getHttpClient() {
    return $this->httpClient;
  }

  /**
   * @param RequestInterface $request
   * @return NuxeoClient
   */
  protected function interceptors($request) {
    foreach($this->interceptors as $interceptor) {
      $interceptor->proceed($request);
    }
    return $this;
  }

}

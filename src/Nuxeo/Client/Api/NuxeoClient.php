<?php
/**
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the GNU Lesser General Public License
 * (LGPL) version 2.1 which accompanies this distribution, and is available at
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * Contributors:
 *     Pierre-Gildas MILLON <pgmillon@nuxeo.com>
 */

namespace Nuxeo\Client\Api;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Guzzle\Common\Exception\GuzzleException;
use Guzzle\Common\Exception\InvalidArgumentException;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Url;
use Guzzle\Plugin\Log\LogPlugin;
use Nuxeo\Client\Api\Marshaller\BlobMarshaller;
use Nuxeo\Client\Api\Marshaller\BlobsMarshaller;
use Nuxeo\Client\Api\Marshaller\NuxeoConverter;
use Nuxeo\Client\Api\Objects\Blob;
use Nuxeo\Client\Api\Objects\Blobs;
use Nuxeo\Client\Api\Objects\Operation;
use Nuxeo\Client\Internals\Spi\Http\EntityEnclosingRequest;
use Nuxeo\Client\Internals\Spi\Http\RequestFactory;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;

AnnotationRegistry::registerLoader('class_exists');

/**
 * Class NuxeoClient
 * @package Nuxeo\Client
 */
class NuxeoClient {

  private $baseUrl;

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

    $this->httpClient->addSubscriber(LogPlugin::getDebugPlugin(true, fopen('debug.txt', 'w+')));

    $this->httpClient->setRequestFactory(new RequestFactory());

    $self = $this;

    /**
     * @param RequestInterface $request
     */
    $this->interceptors[] = function($request) use ($self, $username, $password) {
      try {
        $request->setAuth($username, $password);
      } catch(GuzzleException $ex) {
        throw NuxeoClientException::fromPrevious($ex);
      }
    };

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
   * @see http://explorer.nuxeo.com/nuxeo/site/distribution/current/listOperations List of available operations
   * @param string $operationId
   * @return Operation
   */
  public function automation($operationId = null) {
    $url = clone $this->baseUrl;
    return new Operation($this, $url->addPath(Constants::AUTOMATION_PATH), $operationId);
  }

  /**
   * @param string $name
   * @param string $value
   * @return NuxeoClient
   */
  public function header($name, $value) {
    $self = $this;

    /**
     * @param RequestInterface $request
     */
    $this->interceptors[] = function($request) use ($self, $name, $value) {
      $request->addHeader($name, $value);
    };

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
      $interceptor($request);
    }
    return $this;
  }

}

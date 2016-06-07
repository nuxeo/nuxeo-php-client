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
use Guzzle\Common\Exception\InvalidArgumentException;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Url;
use Nuxeo\Client\Internals\NuxeoClientException;
use Nuxeo\Client\Api\Objects\Operation;

AnnotationRegistry::registerLoader('class_exists');

/**
 * Class NuxeoClient
 * @package Nuxeo\Client
 */
class NuxeoClient {

  protected $baseUrl;

  private $interceptors = array();

  /**
   * @var Client
   */
  private $httpClient;

  /**
   * @param string $url
   * @param string $username
   * @param string $password
   */
  public function __construct($url = 'http://localhost:8080/nuxeo', $username = 'Administrator', $password = 'Administrator') {
    $this->baseUrl = Url::factory($url);
    $this->httpClient = new Client($url);

    $self = $this;

    /**
     * @param RequestInterface $request
     */
    $this->interceptors[] = function($request) use ($self, $username, $password) {
      $request->setAuth($username, $password);
    };

  }

  /**
   * @param string ...
   * @return NuxeoClient
   */
  public function schemas() {
    $this->header(Constants::HEADER_PROPERTIES, join(",", func_get_args()));
    return $this;
  }

  /**
   * @param boolean $value
   * @return NuxeoClient
   */
  public function voidOperation($value) {
    $this->header(Constants::HEADER_VOID_OPERATION, $value?"true":"false");
    return $this;
  }

  /**
   * @see http://explorer.nuxeo.com/nuxeo/site/distribution/current/listOperations List of available operations
   * @param string $operationId
   * @return Operation
   */
  public function automation($operationId) {
    $url = clone $this->baseUrl;
    return new Operation($operationId, $this, $url->addPath(Constants::AUTOMATION_PATH));
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
   * @return Response
   */
  public function post($url, $body = null) {
    try {
      $request = $this->getHttpClient()->createRequest(Request::POST, $url, null, $body);
      $this->interceptors($request);

      return $request->send();
    } catch(RequestException $ex) {
      throw new NuxeoClientException("error", NuxeoClientException::INTERNAL_ERROR_STATUS, $ex);
    } catch(InvalidArgumentException $ex) {
      throw new NuxeoClientException("error", NuxeoClientException::INTERNAL_ERROR_STATUS, $ex);
    }
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

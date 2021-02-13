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


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nuxeo\Client\Response;
use Nuxeo\Client\Spi\Auth\AuthenticationInterceptor;
use Nuxeo\Client\Spi\Interceptor;
use Nuxeo\Client\Spi\SimpleInterceptor;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use function \is_string;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Constants;
use Nuxeo\Client\Request;
use Nuxeo\Client\Spi\NuxeoClientException;
use Psr\Http\Message\UriInterface;

class AbstractConnectable {
  /**
   * @var Client
   */
  private $httpClient;

  /**
   * @var UriInterface
   */
  private $baseUrl;

  /**
   * @var LoggerInterface
   */
  private $logger;

  /**
   * @var Interceptor[]
   */
  private $interceptors = array();

  public function __construct() {
    $logHandler = new StreamHandler('php://stdout', Logger::INFO);
    $this->logger = new Logger('nxPHPClientLogger', [$logHandler]);

    $logHandler->setFormatter(new LineFormatter(null, null, true));
  }

  /**
   * @param LoggerInterface $logger
   */
  public function setLogger($logger) {
    $this->logger = $logger;
  }

  /**
   * @param string|Uri $baseUrl
   * @throws \InvalidArgumentException
   */
  public function setBaseUrl($baseUrl) {
    if (is_string($baseUrl)) {
      $baseUrl = new Uri($baseUrl);
    } elseif (!($baseUrl instanceof UriInterface)) {
      throw new \InvalidArgumentException(
        'URI must be an instance of \Psr\Http\Message\UriInterface or a string'
      );
    }
    if('/' !== substr($baseUrl->getPath(), -1)) {
      $baseUrl = $baseUrl->withPath($baseUrl->getPath().'/');
    }

    $this->baseUrl = $baseUrl;
  }

  /**
   * @return UriInterface
   */
  public function getBaseUrl() {
    return $this->baseUrl;
  }

  /**
   * @return UriInterface
   */
  public function getApiUrl() {
    return UriResolver::resolve($this->getBaseUrl(), new Uri(Constants::API_PATH));
  }

  /**
   * @return Client
   */
  protected function getHttpClient() {
    if(null === $this->httpClient) {
      $stack = HandlerStack::create();
      $stack->push(Middleware::log($this->logger, new MessageFormatter()), 'log');

      $this->httpClient = new Client([
        'base_uri' => $this->baseUrl,
        'handler' => $stack,
        'headers' => [
          'content-type' => Constants::CONTENT_TYPE_JSON,
          'accept' => Constants::CONTENT_TYPE_JSON
        ]
      ]);
    }
    return $this->httpClient;
  }

  /**
   * @return self
   */
  public function debug() {
    /** @var HandlerStack $stack */
    $stack = $this->getHttpClient()->getConfig('handler');

    $stack->remove('log');
    $stack->push(Middleware::log($this->logger, new MessageFormatter(MessageFormatter::DEBUG), LogLevel::INFO), 'log');

    return $this;
  }

  /**
   * @param Request $request
   * @return Request
   * @throws NuxeoClientException
   */
  protected function interceptors(Request $request) {
    $new = $request;
    foreach($this->interceptors as $interceptor) {
      $new = $interceptor->proceed($this->getHttpClient(), $new);
    }
    return $new;
  }

  /**
   * @param $authenticationInterceptor
   * @return self
   */
  protected function withAuthentication($authenticationInterceptor) {
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
   * @return self
   */
  public function header($name, $value) {
    $this->interceptors[] = new SimpleInterceptor(
      function(Request $request) use ($name, $value) {
        return $request->withHeader($name, $value);
      }
    );

    return $this;
  }

  /**
   * @param $request Request
   * @return Response
   * @throws NuxeoClientException
   * @throws GuzzleException
   */
  public function perform($request) {
    $new = $this->interceptors($request);

    return $this->getHttpClient()->send($new, [
      'query' => $new->getQuery(),
      'auth' => $new->getAuth()
    ]);
  }

  /**
   * @param string $method
   * @param string $url
   * @return Request
   */
  public function createRequest($method, $url) {
    return (new Request($method, $url))
      ->withHeader('content-type', 'application/json');
  }

//  /**
//   * @param AbstractMethod $method
//   * @param $params
//   * @throws NuxeoClientException
//   * @return Request
//   */
//  protected function getRequest(AbstractMethod $method, $params) {
//    try {
//      $request = $this->createRequest(
//        $method->getName(),
//        $this->computeRequestUrl($method->computePath($params))
//      );
//
//      return $request;
//    } catch(\InvalidArgumentException $e) {
//      throw NuxeoClientException::fromPrevious($e);
//    }
//  }
}

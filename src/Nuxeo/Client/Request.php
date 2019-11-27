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

namespace Nuxeo\Client;


use GuzzleHttp\Psr7\AppendStream;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request as BaseRequest;
use Nuxeo\Client\Spi\Http\Message\RelatedFile;
use Nuxeo\Client\Spi\Http\Message\RelatedPartInterface;
use Nuxeo\Client\Spi\NuxeoClientException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends BaseRequest {

  const className = __CLASS__;
  const MULTIPART_RELATED = 'multipart/related';

  const GET = 'GET';
  const POST = 'POST';

  protected $relatedFiles = array();

  /**
   * @var AppendStream
   */
  protected $body;

  /**
   * @var StreamInterface
   */
  protected $originalBody;

  protected $originalContentType;

  protected $boundary;

  protected $options = [
    'http_errors' => true
  ];

  /**
   * @param string                               $method  HTTP method
   * @param string|UriInterface                  $uri     URI
   * @param array                                $headers Request headers
   * @param string|null|resource|StreamInterface $body    Request body
   * @param string                               $version Protocol version
   */
  public function __construct($method, $uri, array $headers = [], $body = null, $version = '1.1') {
    parent::__construct($method, $uri, $headers);

    $this->boundary = uniqid('NXPHP-', true);
    $this->body = new AppendStream();
  }

  /**
   * @param RelatedFile $file
   * @param string $contentType
   * @return \GuzzleHttp\Psr7\MessageTrait
   * @throws \Nuxeo\Client\Spi\NuxeoClientException
   */
  public function addRelatedFile($file, $contentType = null) {
    $new = clone $this;

    try {
      $headers = ['Content-Disposition' => $file->getContentDisposition()];
      $contentType = $contentType ?? $file->getContentType();
      if ($contentType) {
        $headers['Content-Type'] = $contentType;
      }
      $new->relatedFiles[] = [
        'name' => 'ignored',
        'contents' => $file->getContent(),
        'headers' => $headers,
        'filename' => $file->getFilename()
      ];
    } catch(\RuntimeException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }

    $new->originalContentType = $this->getHeader('content-type')[0];

    return $new->withHeader('content-type', sprintf('%s;boundary=%s', self::MULTIPART_RELATED, $this->boundary));
  }

  /**
   * @return StreamInterface
   * @throws \InvalidArgumentException
   */
  public function getBody() {
    if($this->relatedFiles) {
      $body = new MultipartStream(array_merge( [[
        'name' => 'ignored',
        'contents' => parent::getBody(),
        'headers' => [
          'Content-Disposition' => RelatedPartInterface::DISPOSITION_INLINE,
          'Content-Type' => $this->originalContentType
        ],
      ]], $this->relatedFiles), $this->boundary);

      return $body;
    }

    return parent::getBody();
  }

  /**
   * @param $name
   * @param $value
   * @return Request
   */
  protected function setOption($name, $value) {
    $new = clone $this;
    if(is_array($value)) {
      $new->options[$name] = $value + $this->getOption($name, []);
    } else {
      $new->options[$name] = $value;
    }
    return $new;
  }

  /**
   * @param $name
   * @param null $default
   * @return mixed|null
   */
  protected function getOption($name, $default = null) {
    return $this->options[$name] ?? $default;
  }

  /**
   * @param array $headers
   * @return Request
   */
  public function withHeaders(array $headers) {
    $new = $this;
    foreach($headers as $name => $value) {
      $new = $new->withHeader($name, $value);
    }

    return $new;
  }

  /**
   * @param array $query
   * @return Request
   */
  public function withQuery(array $query = []) {
    return $this->setOption('query', $query);
  }

  /**
   * @return mixed|null
   */
  public function getQuery() {
    return $this->getOption('query');
  }

  /**
   * @param array $auth
   * @return Request
   */
  public function withAuth(array $auth = []) {
    return $this->setOption('auth', $auth);
  }

  /**
   * @return mixed|null
   */
  public function getAuth() {
    return $this->getOption('auth');
  }


}

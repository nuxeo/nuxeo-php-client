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

namespace Nuxeo\Client;


use GuzzleHttp\Psr7\AppendStream;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request as BaseRequest;
use function GuzzleHttp\Psr7\stream_for;
use Nuxeo\Client\Spi\Http\Message\HeaderFactory;
use Nuxeo\Client\Spi\Http\Message\MultipartRelatedIterator;
use Nuxeo\Client\Spi\Http\Message\RelatedFile;
use Nuxeo\Client\Spi\Http\Message\RelatedPartInterface;
use Nuxeo\Client\Spi\Http\Message\RelatedString;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Zend\Uri\Uri;

class Request extends BaseRequest {

  const className = __CLASS__;
  const MULTIPART_RELATED = 'multipart/related';

  const GET = 'GET';
  const POST = 'POST';

  protected $relatedParts = array();

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
   */
  public function addRelatedFile($file, $contentType = null) {
    $new = clone $this;

    $new->relatedParts[] = [
      'name' => 'ignored',
      'contents' => $file->getContent(),
      'headers' => [
        'content-disposition' => $file->getContentDisposition(),
        'content-type' => $contentType
      ],
      'filename' => $file->getFilename()
    ];
    return $new->withHeader('content-type', sprintf('%s;boundary=%s', self::MULTIPART_RELATED, $this->boundary));
  }

  public function setBody($body, $contentType = null) {
    $new = $this->withBody(stream_for($body));
    $new->originalContentType = $contentType;

    return $new;
  }

  /**
   * @return StreamInterface
   */
  public function getBody() {
    if($this->relatedParts) {
      $this->relatedParts[] = [
        'name' => 'ignored',
        'contents' => parent::getBody(),
        'headers' => [
          'content-disposition' => RelatedPartInterface::DISPOSITION_INLINE
        ],
      ];
      $this->body->addStream(stream_for(new MultipartStream($this->relatedParts, $this->boundary)));

      return $this->body;
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
    return isset($this->options[$name])?$this->options[$name]:$default;
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

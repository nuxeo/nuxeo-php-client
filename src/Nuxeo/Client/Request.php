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


use Guzzle\Http\Message\EntityEnclosingRequest as BaseRequest;
use Guzzle\Http\Message\Response as BaseResponse;
use Guzzle\Http\QueryString;
use Guzzle\Http\Url;
use Nuxeo\Client\Spi\Http\Message\HeaderFactory;
use Nuxeo\Client\Spi\Http\Message\MultipartRelatedIterator;
use Nuxeo\Client\Spi\Http\Message\RelatedFile;
use Nuxeo\Client\Spi\Http\Message\RelatedString;
use Zend\Uri\Uri;

class Request extends BaseRequest {

  const className = __CLASS__;
  const MULTIPART_RELATED = 'multipart/related';

  protected $relatedParts = array();

  protected $originalBody = '';
  protected $originalContentType;

  protected $boundary;

  /**
   * Request constructor.
   * @param string $method
   * @param Uri|string $url
   * @param array $headers
   */
  public function __construct($method, $url, $headers = array()) {
    if($url instanceof Uri) {
      list($username, $password) = $url->getUserInfo()?explode(':', $url->getUserInfo()):array(null, null);

      $guzzle_url = new Url(
        $url->getScheme(),
        $url->getHost(),
        $username,
        $password,
        $url->getPort(),
        $url->getPath(),
        new QueryString($url->getQueryAsArray()),
        $url->getFragment()
      );
    } else {
      $guzzle_url = $url;
    }

    parent::__construct($method, $guzzle_url, $headers);

    $this->boundary = uniqid('NXPHP-', true);
  }

  public function addRelatedFile($filename, $contentType = null) {
    $this->relatedParts[] = new RelatedFile($filename, $contentType);
  }

  public function startResponse(BaseResponse $response) {
    $response->setHeaderFactory(new HeaderFactory());

    return parent::startResponse($response);
  }

  public function setBody($body, $contentType = null) {
    $this->originalBody = $body;
    $this->originalContentType = $contentType;
  }

  /**
   * @return \Guzzle\Http\EntityBody|\Guzzle\Http\EntityBodyInterface|null
   */
  public function getBody() {
    if(!$this->body) {
      $body = '';
      $contentType = $this->originalContentType ?: $this->getHeader('Content-Type');

      if($this->relatedParts) {
        $parts = array_merge(array(new RelatedString(
          $this->originalBody,
          $contentType)), $this->relatedParts);

        foreach(new MultipartRelatedIterator($parts, '--'.$this->boundary) as $part) {
          $body .= $part;
        }

        $contentType = sprintf('%s;boundary=%s', self::MULTIPART_RELATED, $this->boundary);
      } else {
        $body = $this->originalBody;
      }

      parent::setBody($body, $contentType);
    }

    return parent::getBody();
  }

}

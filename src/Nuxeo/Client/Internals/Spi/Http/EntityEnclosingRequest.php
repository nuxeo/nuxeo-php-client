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

namespace Nuxeo\Client\Internals\Spi\Http;

use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\EntityEnclosingRequest as BaseEntityEnclosingRequest;
use Nuxeo\Client\Internals\Spi\Http\Message\MultipartRelatedIterator;
use Nuxeo\Client\Internals\Spi\Http\Message\RelatedFile;
use Nuxeo\Client\Internals\Spi\Http\Message\RelatedString;

class EntityEnclosingRequest extends BaseEntityEnclosingRequest {

  const className = __CLASS__;
  const MULTIPART_RELATED = 'multipart/related';

  protected $relatedParts = array();

  protected $originalBody;
  protected $originalContentType;

  protected $boundary;

  public function __construct($method, $url, $headers = array()) {
    parent::__construct($method, $url, array());

    $this->boundary = uniqid('NXPHP-', true);
  }

  public function addRelatedFile($filename, $contentType = null) {
    $this->relatedParts[] = new RelatedFile($filename, $contentType);
  }

  public function setBody($body, $contentType = null) {
    $this->originalBody = $body;
    $this->originalContentType = $contentType;
  }

  /**
   * @return EntityBody|\Guzzle\Http\EntityBodyInterface|null
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
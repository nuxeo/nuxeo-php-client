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
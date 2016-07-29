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

namespace Nuxeo\Client\Internals\Spi\Http\Message;


class RelatedString implements RelatedPartInterface {

  /**
   * @var string
   */
  protected $contentType;

  /**
   * @var string
   */
  protected $contentLength;

  /**
   * @var string
   */
  protected $contentDisposition;

  /**
   * @var string
   */
  protected $content;

  /**
   * RelatedString constructor.
   * @param string $contentType
   * @param string $contentLength
   * @param string $contentDisposition
   * @param string $content
   */
  public function __construct($content, $contentType, $contentLength = null, $contentDisposition = null) {
    $this->content = $content;
    $this->contentType = $contentType;

    $this->contentLength = $contentLength ?: strlen($content);
    $this->contentDisposition = $contentDisposition ?: self::DISPOSITION_INLINE;
  }

  /**
   * @return string
   */
  public function getContentType() {
    return $this->contentType;
  }

  /**
   * @return string
   */
  public function getContentLength() {
    return $this->contentLength;
  }

  /**
   * @return string
   */
  public function getContentDisposition() {
    return $this->contentDisposition;
  }

  /**
   * @return string
   */
  public function getContent() {
    return $this->content;
  }

}
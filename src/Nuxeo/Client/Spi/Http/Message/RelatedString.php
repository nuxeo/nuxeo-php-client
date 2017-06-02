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

namespace Nuxeo\Client\Spi\Http\Message;


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

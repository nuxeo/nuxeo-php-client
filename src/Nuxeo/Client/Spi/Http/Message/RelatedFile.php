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


use Psr\Http\Message\StreamInterface;

class RelatedFile implements RelatedPartInterface {

  /**
   * @var string
   */
  protected $filename;

  /**
   * @var StreamInterface
   */
  protected $stream;

  /**
   * @var string
   */
  protected $contentType;

  /**
   * RelatedFile constructor.
   * @param $filename
   * @param $stream
   * @param $contentType
   */
  public function __construct($filename, $stream, $contentType) {
    $this->filename = $filename;
    $this->stream = $stream;
    $this->contentType = $contentType;
  }

  public function getContentDisposition() {
    return sprintf('%s;filename="%s"', self::DISPOSITION_ATTACHMENT, $this->filename);
  }

  public function getContent() {
    return $this->stream->getContents();
  }

  public function getContentType() {
    return $this->contentType;
  }

  public function getContentLength() {
    return $this->stream->getSize();
  }

  /**
   * @return string
   */
  public function getFilename() {
    return $this->filename;
  }

}

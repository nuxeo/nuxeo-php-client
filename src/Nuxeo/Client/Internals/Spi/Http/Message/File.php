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

namespace Nuxeo\Client\Internals\Spi\Http\Message;


use Guzzle\Common\Exception\InvalidArgumentException;
use Guzzle\Http\Mimetypes;

class File {

  protected $contentType;
  protected $filename;

  /**
   * @var \SplFileInfo
   */
  protected $file;

  /**
   * @param string $filename Local path to the file
   * @param string $contentType Content-Type of the upload
   */
  public function __construct($filename, $contentType = null) {
    $this->setFilename($filename);
    $this->contentType = $contentType ?: $this->guessContentType();
  }

  public function setFilename($filename) {
    // Remove leading @ symbol
    if(strpos($filename, '@') === 0) {
      $filename = substr($filename, 1);
    }

    if(!is_readable($filename)) {
      throw new InvalidArgumentException("Unable to open {$filename} for reading");
    }

    $this->file = new \SplFileInfo($filename);

    return $this;
  }

  /**
   * @return \SplFileInfo
   */
  public function getFile() {
    return $this->file;
  }

  /**
   * @return string
   */
  public function getFilename() {
    return $this->getFile()->getPathname();
  }

  public function setContentType($type) {
    $this->contentType = $type;

    return $this;
  }

  public function getContentType() {
    return $this->contentType;
  }

  public function getContentLength() {
    return $this->getFile()->getSize();
  }

  /**
   * Determine the Content-Type of the file
   */
  protected function guessContentType() {
    return Mimetypes::getInstance()->fromFilename($this->getFilename()) ?: 'application/octet-stream';
  }
}

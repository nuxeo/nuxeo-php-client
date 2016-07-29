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
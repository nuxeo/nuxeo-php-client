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
 */

namespace Nuxeo\Client\Api\Objects\Blob;

use Nuxeo\Client\Api\Response;
use Nuxeo\Client\Internals\Spi\Http\Header\ContentDisposition;
use Nuxeo\Client\Internals\Spi\NoSuchFileException;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;
use Nuxeo\Client\Internals\Spi\Objects\NuxeoEntity;
use Nuxeo\Client\Internals\Util\IOUtils;


class Blob extends NuxeoEntity {

  const className = __CLASS__;

  /**
   * @var string
   */
  protected $mimeType;

  /**
   * @var \SplFileInfo
   */
  protected $file;

  /**
   * @var string
   */
  protected $filename;

  /**
   * Blob constructor.
   * @param string $filename ASCII-only filename
   * @param \SplFileInfo $file
   * @param string $mimeType
   */
  public function __construct($filename, $file, $mimeType) {
    parent::__construct(null);

    $this->file = $file;
    $this->mimeType = $mimeType;
    $this->filename = $filename;
  }

  /**
   * @param string $filename
   * @param string $mimeType
   * @return Blob
   * @throws NuxeoClientException
   */
  public static function fromFile($filename, $mimeType) {
    $fileInfo = new \SplFileInfo($filename);
    if($fileInfo->isReadable()) {
      return new Blob($fileInfo->getFilename(), $fileInfo, $mimeType);
    } else {
      throw NuxeoClientException::fromPrevious(new NoSuchFileException($filename));
    }
  }

  /**
   * @param Response $response
   * @return Blob
   */
  public static function fromHttpResponse($response) {
    /** @var ContentDisposition $disposition */
    $disposition = $response->getHeader('Content-Disposition');

    return new Blob(
      $disposition->getFilename(),
      IOUtils::copyToTempFile($response->getBody()->getStream()),
      $response->getContentType());
  }

  /**
   * @return string
   */
  public function getMimeType() {
    return $this->mimeType;
  }

  /**
   * @return string
   */
  public function getFilename() {
    return $this->filename;
  }

  /**
   * @return \SplFileInfo
   */
  public function getFile() {
    return $this->file;
  }

}

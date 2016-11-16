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

namespace Nuxeo\Client\Api\Objects;


use Guzzle\Http\Message\Response;
use Nuxeo\Client\Internals\Spi\Http\Header\ContentDisposition;
use Nuxeo\Client\Internals\Spi\NoSuchFileException;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;
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
    $disposition = new ContentDisposition($response->getContentDisposition());

    return new Blob(
      $disposition->getFilename(),
      IOUtils::copyToTempFile($response->getBody()->getStream()),
      $response->getBody()->getContentType());
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
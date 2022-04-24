<?php
/**
 * (C) Copyright 2018 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 */

namespace Nuxeo\Client\Objects\Blob;


use GuzzleHttp\Psr7\Utils;
use function GuzzleHttp\Psr7\stream_for;
use Nuxeo\Client\Response;
use Nuxeo\Client\Spi\NoSuchFileException;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;
use Psr\Http\Message\StreamInterface;


class Blob extends NuxeoEntity {

  /**
   * @var string
   */
  protected $mimeType;

  /**
   * @var StreamInterface
   */
  protected $stream;

  /**
   * @var string
   */
  protected $filename;

  /**
   * Blob constructor.
   * @param string $filename ASCII-only filename
   * @param StreamInterface $stream
   * @param string $mimeType
   */
  public function __construct($filename, $stream, $mimeType) {
    parent::__construct(null);

    $this->stream = $stream;
    $this->mimeType = $mimeType;
    $this->filename = $filename;
  }

  /**
   * @param string $filename
   * @param string $mimeType
   * @return Blob
   * @throws \InvalidArgumentException
   * @throws NuxeoClientException
   */
  public static function fromFile($filename, $mimeType) {
    $fileInfo = new \SplFileInfo($filename);
    if($fileInfo->isReadable()) {
      return new Blob($fileInfo->getFilename(), Utils::streamFor($fileInfo->openFile('rb')), $mimeType);
    }
    throw NuxeoClientException::fromPrevious(new NoSuchFileException($filename));
  }

  /**
   * @param Response $response
   * @return Blob
   */
  public static function fromHttpResponse($response) {
    $disposition = $response->getHeader('Content-Disposition')[0];
    $filename = null;

    foreach(explode(';', $disposition) as $part) {
      if(preg_match('/^filename\*?=/', trim($part))) {
        [$field, $value] = explode('=', $part);
        if(null === $filename || strpos($field, '*') !== false) {
          $filename = $value;
        }
      }
    }

    if(preg_match('/^[^\']+\'\'/', $filename)) {
      [, $filename] = explode('\'\'', $filename);
    }

    return new Blob(
      $filename,
      $response->getBody(),
      $response->getHeaderLine('content-type'));
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
   * @return StreamInterface
   */
  public function getStream() {
    return $this->stream;
  }

}

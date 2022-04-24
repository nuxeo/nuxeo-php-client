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

use GuzzleHttp\Psr7\AppendStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;
use ZBateson\MailMimeParser\MailMimeParser;


class Blobs extends NuxeoEntity implements \Countable {

  /**
   * @var Blob[]
   * @Serializer\Exclude()
   */
  protected $blobs = array();

  /**
   * Blobs constructor.
   * @param Blob[] $blobs
   */
  public function __construct($blobs = array()) {
    parent::__construct(null);

    $this->blobs = $blobs;
  }

  /**
   * @return Blob[]
   */
  public function getBlobs(): array {
    return $this->blobs;
  }

  /**
   * @param Blob $blob
   */
  public function add($blob): void {
    $this->blobs[] = $blob;
  }

  public function count(): int {
    return count($this->blobs);
  }

  /**
   * @param Response $response
   * @return Blobs
   */
  public static function fromHttpResponse(Response $response): Blobs {
    $blobs = [];
    $parser = new MailMimeParser();
    $message = $parser->parse(new AppendStream([
      Utils::streamFor(sprintf("Content-Type: %s\n", $response->getHeaderLine('Content-Type'))),
      $response->getBody()
    ]), true);

    foreach($message->getAllParts() as $part) {
      $copy = Utils::streamFor(fopen('php://temp', 'rb+'));
      Utils::copyToStream($part->getBinaryContentStream(), $copy);

      $blobs[] = new Blob($part->getFilename(), $copy, $part->getContentType());
    }

    return new Blobs($blobs);
  }
}

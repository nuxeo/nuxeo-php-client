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

namespace Nuxeo\Client\Api\Objects;

use JMS\Serializer\Annotation as Serializer;


class Blobs extends NuxeoEntity implements \Countable {

  const className = __CLASS__;

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
  public function getBlobs() {
    return $this->blobs;
  }

  /**
   * @param Blob $blob
   */
  public function add($blob) {
    $this->blobs[] = $blob;
  }

  public function count() {
    return count($this->blobs);
  }
}
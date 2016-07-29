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
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

/**
 *
 * @author Pierre-Gildas MILLON <pgmillon@gmail.com>
 */

namespace Nuxeo\Client\Api\Objects;


class Blob extends NuxeoEntity {

  /**
   * @var string
   */
  protected $mimeType;

  /**
   * @var \SplFileInfo
   */
  protected $file;

  /**
   * Blob constructor.
   * @param \SplFileInfo $file
   * @param string $mimeType
   */
  public function __construct($file, $mimeType) {
    parent::__construct(null);

    $this->file = $file;
    $this->mimeType = $mimeType;
  }

  /**
   * @param string $filename
   * @param string $mimeType
   * @return Blob
   */
  public static function fromFilename($filename, $mimeType) {
    return new Blob(new \SplFileInfo($filename), $mimeType);
  }

  /**
   * @return string
   */
  public function getMimeType() {
    return $this->mimeType;
  }

  /**
   * @return \SplFileInfo
   */
  public function getFile() {
    return $this->file;
  }

}
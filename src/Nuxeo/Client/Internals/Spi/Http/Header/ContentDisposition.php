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

namespace Nuxeo\Client\Internals\Spi\Http\Header;


use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ContentDisposition extends \Zend\Http\Header\ContentDisposition {

  /**
   * @var string
   */
  protected $disposition;

  /**
   * @var string
   */
  protected $filename;

  public function __construct($value) {
    $value = urldecode($value);

    parent::__construct($value);

    foreach(explode(';', $value) as $part) {
      $part = trim($part);

      if(in_array($part, array(ResponseHeaderBag::DISPOSITION_ATTACHMENT, ResponseHeaderBag::DISPOSITION_INLINE), true)) {
        $this->disposition = $part;
      } elseif(preg_match('/^filename\*?=/', $part)) {
        list($field, $filename) = explode('=', $part);

        if(preg_match('/^[^\']+\'\'/', $filename)) {
          list($encoding, $filename) = explode('\'\'', $filename);
        }
        $this->filename = $filename;
      }
    }
  }

  /**
   * @return string
   */
  public function getDisposition() {
    return $this->disposition;
  }

  /**
   * @return string
   */
  public function getFilename() {
    return $this->filename;
  }

}
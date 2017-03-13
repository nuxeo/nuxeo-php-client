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

namespace Nuxeo\Client\Internals\Spi\Http\Header;


use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ContentDisposition extends \Zend\Http\Header\ContentDisposition {

  const className = __CLASS__;

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

    foreach(explode(';', $value) as $part) {
      $part = trim($part);

      if(in_array($part, array(ResponseHeaderBag::DISPOSITION_ATTACHMENT, ResponseHeaderBag::DISPOSITION_INLINE), true)) {
        $this->disposition = $part;
      } elseif(preg_match('/^filename\*?=/', $part)) {
        list(, $filename) = explode('=', $part);

        if(preg_match('/^[^\']+\'\'/', $filename)) {
          list(, $filename) = explode('\'\'', $filename);
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

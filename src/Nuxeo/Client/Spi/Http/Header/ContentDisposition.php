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

namespace Nuxeo\Client\Spi\Http\Header;


use Guzzle\Http\Message\Header;

class ContentDisposition extends Header {

  const className = __CLASS__;

  const DISPOSITION_ATTACHMENT = 'attachment';

  const DISPOSITION_INLINE = 'inline';

  /**
   * @var string
   */
  protected $disposition;

  /**
   * @var string
   */
  protected $filename;

  public function __construct($header, $values) {
    parent::__construct($header, $values);

    array_walk($this->values, function(&$value) {
      $disposition = null;
      $filename = null;

      foreach(explode(';', $value) as $part) {
        $part = trim($part);

        if(in_array($part, array(ContentDisposition::DISPOSITION_ATTACHMENT, ContentDisposition::DISPOSITION_INLINE), true)) {
          $disposition = $part;
        } elseif(preg_match('/^filename\*?=/', $part)) {
          list(, $filename) = explode('=', $part);

          if(preg_match('/^[^\']+\'\'/', $filename)) {
            list(, $filename) = explode('\'\'', $filename);
          }
        }
      }

      $value = array(
        'disposition' => $disposition,
        'filename' => $filename
      );
    });
  }

  /**
   * @param int $index
   * @return string
   * @throws \OutOfRangeException
   */
  public function getDisposition($index = 0) {
    if($index > count($this->values)) {
      throw new \OutOfRangeException();
    }
    return $this->values[$index]['disposition'];
  }

  /**
   * @param int $index
   * @return string
   * @throws \OutOfRangeException
   */
  public function getFilename($index = 0) {
    if($index > count($this->values)) {
      throw new \OutOfRangeException();
    }
    return $this->values[$index]['filename'];
  }

  public function __toString() {
    return implode($this->glue, array_map(function($item) {
      return "${item['disposition']}; filename=${item['filename']}";
    }, $this->values));
  }

}

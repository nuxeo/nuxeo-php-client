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


use Nuxeo\Client\Internals\Spi\InvalidArgumentException;

class ContentDisposition {

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

  /**
   * @var string
   */
  protected $value;

  public function __construct($value = '') {
    $value = urldecode($value);

    foreach(explode(';', $value) as $part) {
      $part = trim($part);

      if(in_array($part, array(self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE), true)) {
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

  public static function fromString($headerLine) {
    $header = new static();

    list($name, $value) = static::splitHeaderLine($headerLine);

    // check to ensure proper header type for this factory
    if (strtolower($name) !== 'content-disposition') {
      throw new InvalidArgumentException('Invalid header line for Content-Disposition string: "' . $name . '"');
    }

    $header->value = $value;

    return $header;
  }

  public function getFieldName() {
    return 'Content-Disposition';
  }

  public function getFieldValue() {
    return $this->value;
  }

  public function toString() {
    return 'Content-Disposition: ' . $this->getFieldValue();
  }

  /**
   * Splits the header line in `name` and `value` parts.
   *
   * @param string $headerLine
   * @return string[] `name` in the first index and `value` in the second.
   * @throws InvalidArgumentException If header does not match with the format ``name:value``
   */
  public static function splitHeaderLine($headerLine)
  {
    $parts = explode(':', $headerLine, 2);
    if (count($parts) !== 2) {
      throw new InvalidArgumentException('Header must match with the format "name:value"');
    }

    $parts[1] = ltrim($parts[1]);

    return $parts;
  }

}

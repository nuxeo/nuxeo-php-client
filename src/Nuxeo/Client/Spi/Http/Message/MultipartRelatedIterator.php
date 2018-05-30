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

namespace Nuxeo\Client\Spi\Http\Message;

use function \get_class;
use Nuxeo\Client\Spi\ClassCastException;

class MultipartRelatedIterator extends \ArrayIterator {

  /**
   * @var string
   */
  protected $boundary;

  public function __construct(array $array, $boundary, $flags = 0) {
    parent::__construct($array, $flags);

    $this->boundary = $boundary;
  }

  /**
   * @throws \Nuxeo\Client\Spi\ClassCastException
   */
  public function current() {
    $part = parent::current();

    if($part instanceof RelatedPartInterface) {
      return sprintf("%s\r\nContent-Type: %s\r\nContent-Length: %d\r\nContent-Disposition: %s\r\n\r\n%s\r\n",
        $this->boundary,
        $part->getContentType(),
        $part->getContentLength(),
        $part->getContentDisposition(),
        $part->getContent()) . ($this->count() - 1 === $this->key() ? $this->boundary . '--' . "\r\n" : '');
    }
    throw new ClassCastException(sprintf('Cannot cast %s as %s', get_class($part), RelatedPartInterface::class));

  }

}

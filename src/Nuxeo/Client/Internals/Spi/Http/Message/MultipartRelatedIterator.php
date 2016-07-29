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

namespace Nuxeo\Client\Internals\Spi\Http\Message;


use Nuxeo\Client\Internals\Spi\ClassCastException;

class MultipartRelatedIterator extends \ArrayIterator {

  /**
   * @var string
   */
  protected $boundary;

  public function __construct(array $array, $boundary, $flags = 0) {
    parent::__construct($array, $flags);

    $this->boundary = $boundary;
  }

  public function current() {
    $part = parent::current();

    if($part instanceof RelatedPartInterface) {
      return sprintf("%s\r\nContent-Type: %s\r\nContent-Length: %d\r\nContent-Disposition: %s\r\n\r\n%s\r\n",
        $this->boundary,
        $part->getContentType(),
        $part->getContentLength(),
        $part->getContentDisposition(),
        $part->getContent()) . ($this->count() - 1 === $this->key() ? $this->boundary . '--' . "\r\n" : '');
    } else {
      throw new ClassCastException(sprintf('Cannot cast %s as %s', get_class($part), RelatedPartInterface::className));
    }

  }

}
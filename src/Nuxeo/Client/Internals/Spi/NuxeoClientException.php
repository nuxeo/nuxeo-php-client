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

namespace Nuxeo\Client\Internals\Spi;

use Exception;

class NuxeoClientException extends \RuntimeException {

  const INTERNAL_ERROR_STATUS = 666;

  public function __construct($message = '', $code = self::INTERNAL_ERROR_STATUS, Exception $previous = null) {
    if(null !== $previous && '' === $message) {
      $message = $previous->getMessage();
    }

    parent::__construct($message, $code, $previous);
  }


  public static function fromPrevious($previous, $message='', $code=self::INTERNAL_ERROR_STATUS) {
    return new NuxeoClientException($message, $code, $previous);
  }

}
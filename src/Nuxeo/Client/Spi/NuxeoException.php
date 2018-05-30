<?php
/**
 * (C) Copyright 2018 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 */

namespace Nuxeo\Client\Spi;

use Exception;


class NuxeoException extends \Exception {

  public function __construct($message = '', $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);
  }

  /**
   * @param $trace
   * @return $this
   * @throws \ReflectionException
   */
  public function setTrace($trace) {
    $property = new \ReflectionProperty('Exception', 'trace');
    $property->setAccessible(true);
    $property->setValue($this, $trace);
    return $this;
  }

  /**
   * @param string $file
   * @param string $line
   * @return $this
   */
  public function setLocation($file, $line) {
    $this->file = $file;
    $this->line = $line;
    return $this;
  }

}

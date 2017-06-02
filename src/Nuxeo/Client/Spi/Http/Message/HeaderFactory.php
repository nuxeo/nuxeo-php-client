<?php
/**
 * (C) Copyright 2017 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 */

namespace Nuxeo\Client\Spi\Http\Message;

use Guzzle\Http\Message\Header\HeaderFactory as BaseFactory;
use Nuxeo\Client\Spi\Http\Header\ContentDisposition;

class HeaderFactory extends BaseFactory {

  /**
   * HeaderFactory constructor.
   */
  public function __construct() {
    $this->mapping['content-disposition'] = ContentDisposition::className;
  }
}

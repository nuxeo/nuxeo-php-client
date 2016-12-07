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

namespace Nuxeo\Client\Internals\Spi\Serializer;

use JMS\Serializer\JsonSerializationVisitor as BaseSerializationVisitor;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

/**
 * Class JsonSerializationVisitor
 *
 * This class exists to provide the equivalent of the PHP 5.4+ JSON_UNESCAPED_SLASHES option
 *
 * @package Nuxeo\Client\Internals\Spi\Serializer
 */
class JsonSerializationVisitor extends BaseSerializationVisitor {

  public function __construct(PropertyNamingStrategyInterface $namingStrategy) {
    parent::__construct($namingStrategy);

    if(defined('JSON_UNESCAPED_SLASHES')) {
      $this->setOptions(JSON_UNESCAPED_SLASHES);
    }

  }

  public function getResult() {
    if(!defined('JSON_UNESCAPED_SLASHES')) {
      return str_replace('\/', '/', parent::getResult());
    } else {
      return parent::getResult();
    }
  }

}
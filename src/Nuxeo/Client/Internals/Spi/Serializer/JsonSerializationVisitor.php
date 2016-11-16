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

namespace Nuxeo\Client\Internals\Spi\Serializer;

use JMS\Serializer\JsonSerializationVisitor as BaseSerializationVisitor;

/**
 * Class JsonSerializationVisitor
 *
 * This class exists to provide the equivalent of the PHP 5.4+ JSON_UNESCAPED_SLASHES option
 *
 * @package Nuxeo\Client\Internals\Spi\Serializer
 */
class JsonSerializationVisitor extends BaseSerializationVisitor {

  public function getResult() {
    return str_replace('\/', '/', parent::getResult());
  }

}
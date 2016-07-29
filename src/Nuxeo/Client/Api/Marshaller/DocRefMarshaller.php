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

namespace Nuxeo\Client\Api\Marshaller;


use Nuxeo\Client\Api\Objects\DocRef;

class DocRefMarshaller implements NuxeoMarshaller {

  /**
   * @param DocRef $object
   * @return string
   */
  public function write($object) {
    return 'doc:'.$object->getRef();
  }

  /**
   * @param string $in
   * @return DocRef
   */
  public function read($in) {
    return new DocRef($in);
  }

}
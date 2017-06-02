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

namespace Nuxeo\Client\Marshaller;


use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\VisitorInterface;

class BlobMarshaller implements NuxeoMarshaller {

  /**
   * @param $in
   * @param VisitorInterface $visitor
   * @param DeserializationContext $context
   * @return null
   */
  public function read($in, VisitorInterface $visitor, DeserializationContext $context) {
    return null;
  }

  /**
   * @param $object
   * @param VisitorInterface $visitor
   * @param SerializationContext $context
   * @return null
   */
  public function write($object, VisitorInterface $visitor, SerializationContext $context) {
    return null;
  }

}

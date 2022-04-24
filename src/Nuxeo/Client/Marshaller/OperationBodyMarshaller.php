<?php
/*
 * (C) Copyright 2022 Nuxeo SA (http://nuxeo.com/) and contributors.
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

namespace Nuxeo\Client\Marshaller;


use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Nuxeo\Client\Objects\Blob\Blob;
use Nuxeo\Client\Spi\Objects\Operation\OperationBody;

class OperationBodyMarshaller implements NuxeoMarshaller {

  public function read($in, DeserializationVisitorInterface $visitor, DeserializationContext $context) {
    return $context->getNavigator()->accept($in, ['name' => 'array', 'params' => [['name' => OperationBody::class]]]);
  }

  public function write($object, SerializationVisitorInterface $visitor, SerializationContext $context) {
    if($object instanceof OperationBody) {
      $result = [
        'params' => $object->serializeParams()
      ];

      if(($input = $object->getInput()) && !$input instanceof Blob) {
        $result['input'] = $context->getNavigator()->accept($input);
      }

      return $result;
    }
  }
}

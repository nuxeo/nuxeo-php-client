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

namespace Nuxeo\Client\Marshaller;


use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Nuxeo\Client\NuxeoClient;
use Nuxeo\Client\Objects\Operation\DocRef;

class DocRefMarshaller implements NuxeoMarshaller {

  /**
   * @var NuxeoClient
   */
  protected $nuxeoClient;

  /**
   * DocRefMarshaller constructor.
   * @param NuxeoClient $nuxeoClient
   */
  public function __construct(NuxeoClient $nuxeoClient) {
    $this->nuxeoClient = $nuxeoClient;
  }

  /**
   * @param DocRef $object
   * @param SerializationVisitorInterface $visitor
   * @param SerializationContext $context
   * @return string
   */
  public function write($object, SerializationVisitorInterface $visitor, SerializationContext $context) {
    return 'doc:'.$object->getRef();
  }

  /**
   * @param $in
   * @param DeserializationVisitorInterface $visitor
   * @param DeserializationContext $context
   * @return DocRef
   */
  public function read($in, DeserializationVisitorInterface $visitor, DeserializationContext $context) {
    return new DocRef($in, $this->nuxeoClient);
  }

}

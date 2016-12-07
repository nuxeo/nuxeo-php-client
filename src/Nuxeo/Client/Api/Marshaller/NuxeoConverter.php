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

namespace Nuxeo\Client\Api\Marshaller;


use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\VisitorInterface;
use Nuxeo\Client\Internals\Spi\Serializer\JsonSerializationVisitor;

class NuxeoConverter {

  /**
   * @var NuxeoMarshaller[]
   */
  protected $marshallers = array();

  /**
   * @var Serializer
   */
  private $serializer = null;

  /**
   * @param string $type
   * @param NuxeoMarshaller $marshaller
   * @return NuxeoConverter
   */
  public function registerMarshaller($type, $marshaller) {
    $this->marshallers[$type] = $marshaller;
    return $this;
  }

  /**
   * @return NuxeoMarshaller[]
   */
  public function getMarshallers() {
    return $this->marshallers;
  }

  /**
   * @param string $type
   * @return NuxeoMarshaller
   */
  public function getMarshaller($type) {
    return $this->marshallers[$type];
  }

  /**
   * @param mixed $object
   * @return string
   */
  public function writeJSON($object) {
    return $this->getSerializer()->serialize($object, 'json');
  }

  /**
   * @param string $data
   * @param string $type
   * @return mixed
   */
  public function readJSON($data, $type) {
    return $this->getSerializer()->deserialize($data, $type, 'json');
  }

  /**
   * @return Serializer
   */
  protected function getSerializer() {
    if(null === $this->serializer) {
      $strategy = new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy());

      $self = $this;

      $this->serializer = SerializerBuilder::create()
        ->setSerializationVisitor('json', new JsonSerializationVisitor($strategy))
        ->setDeserializationVisitor('json', new JsonDeserializationVisitor($strategy))
        ->configureHandlers(function(HandlerRegistry $registry) use ($self) {
          foreach($self->getMarshallers() as $type => $marshaller) {
            $registry->registerHandler(
              GraphNavigator::DIRECTION_SERIALIZATION,
              $type,
              'json',
              function(VisitorInterface $visitor, $object, array $type, SerializationContext $context) use ($self) {
                $marshaller = $self->getMarshaller($type['name']);
                return $marshaller->write($object, $visitor, $context);
              }
            );
            $registry->registerHandler(
              GraphNavigator::DIRECTION_DESERIALIZATION,
              $type,
              'json',
              function(VisitorInterface $visitor, $object, array $type, DeserializationContext $context) use ($self) {
                $marshaller = $self->getMarshaller($type['name']);
                return $marshaller->read($object, $visitor, $context);
              }
            );
          }
        })
        ->build();
    }
    return $this->serializer;
  }

}
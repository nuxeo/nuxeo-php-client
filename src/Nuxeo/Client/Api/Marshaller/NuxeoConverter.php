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


use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\VisitorInterface;

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
   * @param mixed $object
   * @return string
   */
  public function write($object) {
    return $this->getSerializer()->serialize($object, 'json');
  }

  /**
   * @param string $data
   * @param string $clazz
   * @return mixed
   */
  public function read($data, $clazz) {
    return $this->getSerializer()->deserialize($data, $clazz, 'json');
  }

  /**
   * @return Serializer
   */
  protected function getSerializer() {
    if(null === $this->serializer) {
      $strategy = new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy());

      $jsonSerializer = new JsonSerializationVisitor($strategy);
      $jsonSerializer->setOptions(JSON_UNESCAPED_SLASHES);

      $self = $this;

      $this->serializer = SerializerBuilder::create()
        ->setSerializationVisitor('json', $jsonSerializer)
        ->setDeserializationVisitor('json', new JsonDeserializationVisitor($strategy))
        ->configureHandlers(function(HandlerRegistry $registry) use ($self) {
          foreach(array_keys($self->marshallers) as $type) {
            $registry->registerHandler(
              GraphNavigator::DIRECTION_SERIALIZATION,
              $type,
              'json',
              function(VisitorInterface $visitor, $object, array $type) use ($self) {
                $marshaller = $self->marshallers[$type['name']];
                return $marshaller->write($object);
              }
            );
            $registry->registerHandler(
              GraphNavigator::DIRECTION_DESERIALIZATION,
              $type,
              'json',
              function(VisitorInterface $visitor, $object, array $type) use ($self) {
                $marshaller = $self->marshallers[$type['name']];
                return $marshaller->read($object);
              }
            );
          }
        })
        ->build();
    }
    return $this->serializer;
  }

}
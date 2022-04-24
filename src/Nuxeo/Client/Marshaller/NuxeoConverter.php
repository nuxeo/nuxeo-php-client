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
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\Factory\JsonDeserializationVisitorFactory;
use JMS\Serializer\Visitor\Factory\JsonSerializationVisitorFactory;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Nuxeo\Client\Objects\Document;
use Nuxeo\Client\Objects\Documents;
use Nuxeo\Client\Spi\NuxeoClientException;

class NuxeoConverter {

  /**
   * @var NuxeoMarshaller[]
   */
  protected $marshallers = array();

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
    return $this->getSerializer()->serialize($object,'json');
  }

  /**
   * @param string $data
   * @param string $type
   * @return mixed
   * @throws NuxeoClientException
   */
  public function readJSON($data, $type = null) {
    $array_data = $this->getSerializer()->deserialize($data, 'array', 'json');
    if(null === $type) {
      $type = 'array';

      if(array_key_exists('entity-type', $array_data)) {
        $entityType = $array_data['entity-type'];
        $typeMap = array(
          'document' => Document::class,
          'documents' => Documents::class,
        );

        if(array_key_exists($entityType, $typeMap)) {
          $type = $typeMap[$entityType];
        }
      }
    }

    return $type === 'array' ? $array_data : $this->readData($array_data, $type);
  }

  /**
   * @param string|array $data
   * @param string $type
   * @return mixed
   */
  public function readData($data, $type) {
    return $this->getSerializer()->fromArray(is_array($data) ? $data : [$data], $type);
  }

  protected function getSerializer() {
    $self = $this;

    return SerializerBuilder::create()
      ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
      ->setSerializationVisitor('json', (new JsonSerializationVisitorFactory())->setOptions(JSON_UNESCAPED_SLASHES))
      ->setDeserializationVisitor('json', (new JsonDeserializationVisitorFactory())->setOptions(JSON_UNESCAPED_SLASHES))
      ->configureHandlers(function(HandlerRegistry $registry) use ($self) {
        $registry->registerSubscribingHandler(new DateHandler());
        foreach($this->getMarshallers() as $type => $marshaller) {
          $registry->registerHandler(
            GraphNavigatorInterface::DIRECTION_SERIALIZATION,
            $type,
            'json',
            function(SerializationVisitorInterface $visitor, $object, array $type, SerializationContext $context) use ($self) {
              return $self->getMarshaller($type['name'])->write($object, $visitor, $context);
            }
          );
          $registry->registerHandler(
            GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
            $type,
            'json',
            function(DeserializationVisitorInterface $visitor, $object, array $type, DeserializationContext $context) use ($self) {
              return $self->getMarshaller($type['name'])->read($object, $visitor, $context);
            }
          );
        }

      })
      ->build();
  }

}

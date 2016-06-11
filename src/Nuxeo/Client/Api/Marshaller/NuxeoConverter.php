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

/**
 *
 * @author Pierre-Gildas MILLON <pgmillon@gmail.com>
 */

namespace Nuxeo\Client\Api\Marshaller;


use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class NuxeoConverter {

  /**
   * @var NuxeoMarshaller[]
   */
  protected $marshallers = array();

  /**
   * @var Serializer
   */
  protected $serializer;

  /**
   * NuxeoConverter constructor.
   */
  public function __construct() {
    $this->serializer = SerializerBuilder::create()
      ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
      ->build();
  }

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
    $clazz = get_class($object);
    if(array_key_exists($clazz, $this->marshallers)) {
      $marshaller = $this->marshallers[$clazz];
      return $marshaller->write($object);
    }

    return $this->serializer->serialize($object, 'json');
  }

  /**
   * @param string $data
   * @param string $clazz
   * @return mixed
   */
  public function read($data, $clazz) {
    if(array_key_exists($clazz, $this->marshallers)) {
      $marshaller = $this->marshallers[$clazz];
      return $marshaller->read($data);
    }

    return $this->serializer->deserialize($data, $clazz, 'json');
  }

}
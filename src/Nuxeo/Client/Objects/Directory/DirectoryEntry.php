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

namespace Nuxeo\Client\Objects\Directory;


use JMS\Serializer\Annotation as Serializer;

class DirectoryEntry {

  const MSG_ID_LABEL_MANDATORY = 'A DirectoryEntry must contain both an id and a label';

  /**
   * @var int
   * @Serializer\Type("integer")
   */
  private $ordering;

  /**
   * @var int
   * @Serializer\Type("integer")
   */
  private $obsolete;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $id;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $label;

  /**
   * DirectoryEntry constructor.
   * @param string $id
   * @param string $label
   * @throws \InvalidArgumentException
   */
  public function __construct($id, $label) {
    if(empty($id) || empty($label)) {
      throw new \InvalidArgumentException(self::MSG_ID_LABEL_MANDATORY);
    }
    $this->id = $id;
    $this->label = $label;
  }

  /**
   * @return int
   */
  public function getOrdering() {
    return $this->ordering;
  }

  /**
   * @param int $ordering
   * @return DirectoryEntry
   */
  public function setOrdering($ordering) {
    $this->ordering = $ordering;
    return $this;
  }

  /**
   * @return int
   */
  public function getObsolete() {
    return $this->obsolete;
  }

  /**
   * @param int $obsolete
   * @return DirectoryEntry
   */
  public function setObsolete($obsolete) {
    $this->obsolete = $obsolete;
    return $this;
  }

  /**
   * @return string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getLabel() {
    return $this->label;
  }

}

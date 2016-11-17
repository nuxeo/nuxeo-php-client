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

namespace Nuxeo\Automation\Client;


/**
 * Document class
 *
 * hold a return document
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 * @deprecated Use \Nuxeo\Client\Api\Objects\Document
 */
class NuxeoDocument {

  private $object;
  private $properties;

  public function __construct($newDocument) {
    $this->object = $newDocument;
    if(array_key_exists('properties', $this->object)) {
      $this->properties = $this->object['properties'];
    }
  }

  public function getUid() {
    return $this->object['uid'];
  }

  public function getPath() {
    return $this->object['path'];
  }

  public function getType() {
    return $this->object['type'];
  }

  public function getState() {
    return $this->object['state'];
  }

  public function getTitle() {
    return $this->object['title'];
  }

  public function output() {
    $value = count($this->object);

    for ($test = 0; $test < $value - 1; $test++) {
      echo '<td> ' . current($this->object) . '</td>';
      next($this->object);
    }

    if ($this->properties !== NULL) {
      $value = count($this->properties);
      for ($test = 0; $test < $value; $test++) {
        echo '<td>' . key($this->properties) . ' : ' .
          current($this->properties) . '</td>';
        next($this->properties);
      }
    }
  }

  public function getObject() {
    return $this->object;
  }

  public function getProperty($schemaNamePropertyName) {
    if (array_key_exists($schemaNamePropertyName, $this->properties)) {
      return $this->properties[$schemaNamePropertyName];
    } else {
      return null;
    }
  }
}

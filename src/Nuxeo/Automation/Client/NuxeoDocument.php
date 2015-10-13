<?php
/*
 * (C) Copyright 2015 Nuxeo SA (http://nuxeo.com/) and contributors.
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

namespace Nuxeo\Automation\Client;


/**
 * Document class
 *
 * hold a return document
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class NuxeoDocument {

  Private $object;
  Private $properties;

  Public function __construct($newDocument) {
    $this->object = $newDocument;
    if (array_key_exists('properties', $this->object))
      $this->properties = $this->object['properties'];
    else
      $this->properties = null;
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

  Public function output() {
    $value = sizeof($this->object);

    for ($test = 0; $test < $value - 1; $test++) {
      echo '<td> ' . current($this->object) . '</td>';
      next($this->object);
    }

    if ($this->properties !== NULL) {
      $value = sizeof($this->properties);
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
    }
    else
      return null;
  }
}

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

namespace Nuxeo\Client\Api\Objects;


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Api\Constants;

class Document extends NuxeoEntity {

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $path;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $type;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $state;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $lockOwner;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $lockCreated;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $versionLabel;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $isCheckedOut;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $lastModified;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $changeToken;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $parentRef;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $uid;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $title;

  /**
   * @var object[]
   * @Serializer\Type("array")
   */
  private $properties;

  /**
   * @var string[]
   * @Serializer\Type("array<string>")
   */
  private $facets;

  /**
   * Document constructor.
   */
  public function __construct() {
    parent::__construct(Constants::ENTITY_TYPE_DOCUMENT);
  }

  /**
   * @param $name
   * @return object
   */
  public function getProperty($name) {
    if(array_key_exists($name, $this->properties)) {
      return $this->properties[$name];
    } else {
      return null;
    }
  }

  /**
   * @return string
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * @param string $path
   * @return Document
   */
  public function setPath($path) {
    $this->path = $path;
    return $this;
  }

  /**
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @param string $type
   * @return Document
   */
  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * @return string
   */
  public function getState() {
    return $this->state;
  }

  /**
   * @param string $state
   * @return Document
   */
  public function setState($state) {
    $this->state = $state;
    return $this;
  }

  /**
   * @return string
   */
  public function getLockOwner() {
    return $this->lockOwner;
  }

  /**
   * @param string $lockOwner
   * @return Document
   */
  public function setLockOwner($lockOwner) {
    $this->lockOwner = $lockOwner;
    return $this;
  }

  /**
   * @return string
   */
  public function getLockCreated() {
    return $this->lockCreated;
  }

  /**
   * @param string $lockCreated
   * @return Document
   */
  public function setLockCreated($lockCreated) {
    $this->lockCreated = $lockCreated;
    return $this;
  }

  /**
   * @return string
   */
  public function getVersionLabel() {
    return $this->versionLabel;
  }

  /**
   * @param string $versionLabel
   * @return Document
   */
  public function setVersionLabel($versionLabel) {
    $this->versionLabel = $versionLabel;
    return $this;
  }

  /**
   * @return string
   */
  public function getIsCheckedOut() {
    return $this->isCheckedOut;
  }

  /**
   * @param string $isCheckedOut
   * @return Document
   */
  public function setIsCheckedOut($isCheckedOut) {
    $this->isCheckedOut = $isCheckedOut;
    return $this;
  }

  /**
   * @return string
   */
  public function getLastModified() {
    return $this->lastModified;
  }

  /**
   * @param string $lastModified
   * @return Document
   */
  public function setLastModified($lastModified) {
    $this->lastModified = $lastModified;
    return $this;
  }

  /**
   * @return string
   */
  public function getChangeToken() {
    return $this->changeToken;
  }

  /**
   * @param string $changeToken
   * @return Document
   */
  public function setChangeToken($changeToken) {
    $this->changeToken = $changeToken;
    return $this;
  }

  /**
   * @return string
   */
  public function getParentRef() {
    return $this->parentRef;
  }

  /**
   * @param string $parentRef
   * @return Document
   */
  public function setParentRef($parentRef) {
    $this->parentRef = $parentRef;
    return $this;
  }

  /**
   * @return string
   */
  public function getUid() {
    return $this->uid;
  }

  /**
   * @param string $uid
   * @return Document
   */
  public function setUid($uid) {
    $this->uid = $uid;
    return $this;
  }

  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param string $title
   * @return Document
   */
  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  /**
   * @return string[]
   */
  public function getFacets() {
    return $this->facets;
  }

  /**
   * @param string[] $facets
   * @return Document
   */
  public function setFacets($facets) {
    $this->facets = $facets;
    return $this;
  }

  /**
   * @return object[]
   */
  public function getProperties() {
    return $this->properties;
  }

  /**
   * @param object[] $properties
   */
  public function setProperties($properties) {
    $this->properties = $properties;
  }

}
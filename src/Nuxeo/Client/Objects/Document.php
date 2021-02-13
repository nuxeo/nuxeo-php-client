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

namespace Nuxeo\Client\Objects;


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Constants;
use Nuxeo\Client\NuxeoClient;
use Nuxeo\Client\Spi\ClassCastException;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;

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
   * @var mixed[]
   * @Serializer\Type("array")
   */
  private $properties;

  /**
   * @var string[]
   * @Serializer\Type("array<string>")
   */
  private $facets;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $name;

  /**
   * Document constructor.
   * @param NuxeoClient $nuxeoClient
   */
  public function __construct($nuxeoClient = null) {
    parent::__construct(Constants::ENTITY_TYPE_DOCUMENT, $nuxeoClient);
  }

  /**
   * @param NuxeoClient $nuxeoClient
   * @return self
   */
  public static function create($nuxeoClient = null) {
    return new self($nuxeoClient);
  }

  /**
   * @param $name
   * @param string $type
   * @param NuxeoClient $nuxeoClient
   * @return self
   */
  public static function createWithName($name, $type = null, $nuxeoClient = null) {
    return (new self($nuxeoClient))
      ->setName($name)
      ->setType($type);
  }

  /**
   * @param $name
   * @param $type
   * @return string|mixed
   * @throws \Doctrine\Common\Annotations\AnnotationException
   */
  public function getProperty($name, $type = null) {
    if(array_key_exists($name, $this->properties)) {
      if(null !== $type && $this->getNuxeoClient()) {
        return $this->getConverter()->readData($this->properties[$name], $type);
      }
      return $this->properties[$name];
    }
    return null;
  }

  /**
   * @param string $name
   * @param mixed $value
   * @return self
   */
  public function setProperty($name, $value) {
    $this->properties[$name] = $value;
    return $this;
  }

  /**
   * @return Blob\Blob
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchBlob() {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()->repository()->fetchBlobById($this->getUid(), Constants::DEFAULT_FILE_CONTENT);
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @return Documents
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchChildren() {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()->repository()->fetchChildrenById($this->getUid());
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @return Workflow\Workflows
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchWorkflows() {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()->workflows()->fetchWorkflowsById($this->getUid());
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @param string $workflowModelName
   * @return Workflow\Workflow
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function startWorkflow($workflowModelName) {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()->workflows()->startWorkflowByNameWithDocId($workflowModelName, $this->getUid());
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  public function fetchTasks() {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()->workflows()->fetchTasksByDocumentId($this->getUid());
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @return string
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * @param string $path
   * @return self
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
   * @return self
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
   * @return self
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
   * @return self
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
   * @return self
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
   * @return self
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
   * @return self
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
   * @return self
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
   * @return self
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
   * @return self
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
   * @return self
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
   * @return self
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
   * @return self
   */
  public function setFacets($facets) {
    $this->facets = $facets;
    return $this;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string $name
   * @return self
   */
  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  /**
   * @return mixed[]
   */
  public function getProperties() {
    return $this->properties;
  }

  /**
   * @param mixed[] $properties
   * @return self
   */
  public function setProperties($properties) {
    $this->properties = $properties;
    return $this;
  }

}

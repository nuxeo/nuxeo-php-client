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
 */

namespace Nuxeo\Client\Api\Objects\Audit;


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Internals\Spi\Objects\NuxeoEntity;

class LogEntry extends NuxeoEntity {

  const className = __CLASS__;

  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  protected $id;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $category;

  /**
   * @var string
   * @Serializer\Type("string")
   * @Serializer\SerializedName("principal")
   */
  protected $principalName;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $comment;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $docLifeCycle;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $docPath;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $docType;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $docUUID;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $eventId;

  /**
   * @var string
   * @Serializer\Type("string")
   * @Serializer\SerializedName("repoId")
   */
  protected $repositoryId;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $eventDate;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $logDate;

  public function __construct() {
    parent::__construct(Constants::ENTITY_TYPE_LOG_ENTRY);
  }

  /**
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getCategory() {
    return $this->category;
  }

  /**
   * @param string $category
   */
  public function setCategory($category) {
    $this->category = $category;
  }

  /**
   * @return string
   */
  public function getPrincipalName() {
    return $this->principalName;
  }

  /**
   * @param string $principalName
   */
  public function setPrincipalName($principalName) {
    $this->principalName = $principalName;
  }

  /**
   * @return string
   */
  public function getComment() {
    return $this->comment;
  }

  /**
   * @param string $comment
   */
  public function setComment($comment) {
    $this->comment = $comment;
  }

  /**
   * @return string
   */
  public function getDocLifeCycle() {
    return $this->docLifeCycle;
  }

  /**
   * @param string $docLifeCycle
   */
  public function setDocLifeCycle($docLifeCycle) {
    $this->docLifeCycle = $docLifeCycle;
  }

  /**
   * @return string
   */
  public function getDocPath() {
    return $this->docPath;
  }

  /**
   * @param string $docPath
   */
  public function setDocPath($docPath) {
    $this->docPath = $docPath;
  }

  /**
   * @return string
   */
  public function getDocType() {
    return $this->docType;
  }

  /**
   * @param string $docType
   */
  public function setDocType($docType) {
    $this->docType = $docType;
  }

  /**
   * @return string
   */
  public function getDocUUID() {
    return $this->docUUID;
  }

  /**
   * @param string $docUUID
   */
  public function setDocUUID($docUUID) {
    $this->docUUID = $docUUID;
  }

  /**
   * @return string
   */
  public function getEventId() {
    return $this->eventId;
  }

  /**
   * @param string $eventId
   */
  public function setEventId($eventId) {
    $this->eventId = $eventId;
  }

  /**
   * @return string
   */
  public function getRepositoryId() {
    return $this->repositoryId;
  }

  /**
   * @param string $repositoryId
   */
  public function setRepositoryId($repositoryId) {
    $this->repositoryId = $repositoryId;
  }

  /**
   * @return string
   */
  public function getEventDate() {
    return $this->eventDate;
  }

  /**
   * @param string $eventDate
   */
  public function setEventDate($eventDate) {
    $this->eventDate = $eventDate;
  }

  /**
   * @return string
   */
  public function getLogDate() {
    return $this->logDate;
  }

  /**
   * @param string $logDate
   */
  public function setLogDate($logDate) {
    $this->logDate = $logDate;
  }

}

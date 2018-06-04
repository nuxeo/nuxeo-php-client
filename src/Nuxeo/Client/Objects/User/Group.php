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

namespace Nuxeo\Client\Objects\User;


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Constants;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;

class Group extends NuxeoEntity {

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $groupname;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $grouplabel;

  public function __construct($nuxeoClient = null) {
    parent::__construct(Constants::ENTITY_TYPE_GROUP, $nuxeoClient);
  }

  /**
   * @return self
   * @throws \Nuxeo\Client\Spi\NuxeoClientException
   */
  public function save() {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()
        ->userManager()
        ->updateGroup($this->getGroupName(), $this);
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @param string $username
   * @return Group
   */
  public function addUser($username) {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()
        ->userManager()
        ->attachUserToGroup($this->getGroupName(), $username);
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @return Users
   */
  public function fetchUsers() {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()
        ->userManager()
        ->fetchGroupUsers($this->getGroupName());
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @return Users
   */
  public function fetchGroups() {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()
        ->userManager()
        ->fetchGroupGroups($this->getGroupName());
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @return mixed
   */
  public function getGroupName() {
    return $this->groupname;
  }

  /**
   * @param string $groupName
   * @return self
   */
  public function setGroupName($groupName) {
    $this->groupname = $groupName;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getGroupLabel() {
    return $this->grouplabel;
  }

  /**
   * @param string $groupLabel
   * @return Group
   */
  public function setGroupLabel($groupLabel) {
    $this->grouplabel = $groupLabel;
    return $this;
  }

}

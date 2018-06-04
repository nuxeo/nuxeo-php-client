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

class User extends NuxeoEntity {

  const FIRST_NAME_PROPERTY = 'firstName';

  const LAST_NAME_PROPERTY = 'lastName';

  const EMAIL_PROPERTY = 'email';

  const GROUPS_PROPERTY = 'groups';

  const USERNAME_PROPERTY = 'username';

  const COMPANY_PROPERTY = 'company';

  const PASSWORD_PROPERTY = 'password';

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $id;

  /**
   * @var string[]
   * @Serializer\Type("array")
   */
  private $properties = [];

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $username;

  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  private $isAdministrator;

  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  private $isAnonymous;

  public function __construct() {
    parent::__construct(Constants::ENTITY_TYPE_USER);
  }

  /**
   * @return self
   * @throws NuxeoClientException
   */
  public function save() {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()
        ->userManager()
        ->updateUser($this->getUsername(), $this);
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @param string $groupName
   * @return User
   */
  public function addGroup($groupName) {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()
        ->userManager()
        ->attachGroupToUser($this->getUsername(), $groupName);
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @return Groups
   */
  public function fetchGroups() {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()
        ->userManager()
        ->fetchUserGroups($this->getUsername());
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @return string
   */
  public function getUsername() {
    if(null === $this->username) {
      return $this->properties[self::USERNAME_PROPERTY];
    }
    return $this->username;
  }

  /**
   * @param string $username
   * @return self
   */
  public function setUsername($username) {
    $this->username = $username;
    $this->properties[self::USERNAME_PROPERTY] = $username;
    return $this;
  }

  /**
   * @return boolean
   */
  public function isAdministrator() {
    return $this->isAdministrator;
  }

  /**
   * @return boolean
   */
  public function isAnonymous() {
    return $this->isAnonymous;
  }

  /**
   * @return string
   */
  public function getFirstName() {
    return $this->properties[self::FIRST_NAME_PROPERTY];
  }

  /**
   * @return string
   */
  public function getLastName() {
    return $this->properties[self::LAST_NAME_PROPERTY];
  }

  /**
   * @return string
   */
  public function getEmail() {
    return $this->properties[self::EMAIL_PROPERTY];
  }

  /**
   * @return string
   */
  public function getPassword() {
    return $this->properties[self::PASSWORD_PROPERTY];
  }

  /**
   * @return string
   */
  public function getCompany() {
    return $this->properties[self::COMPANY_PROPERTY];
  }

  /**
   * @return string
   */
  public function getGroups() {
    return $this->properties[self::GROUPS_PROPERTY];
  }

  /**
   * @param string $value
   * @return self
   */
  public function setFirstName($value) {
    $this->properties[self::FIRST_NAME_PROPERTY] = $value;
    return $this;
  }

  /**
   * @param string $value
   * @return self
   */
  public function setLastName($value) {
    $this->properties[self::LAST_NAME_PROPERTY] = $value;
    return $this;
  }

  /**
   * @param string $value
   * @return self
   */
  public function setEmail($value) {
    $this->properties[self::EMAIL_PROPERTY] = $value;
    return $this;
  }

  /**
   * @param string $value
   * @return self
   */
  public function setPassword($value) {
    $this->properties[self::PASSWORD_PROPERTY] = $value;
    return $this;
  }

  /**
   * @param string $value
   * @return self
   */
  public function setCompany($value) {
    $this->properties[self::COMPANY_PROPERTY] = $value;
    return $this;
  }

  /**
   * @param string $value
   * @return self
   */
  public function setGroups($value) {
    $this->properties[self::GROUPS_PROPERTY] = $value;
    return $this;
  }

  /**
   * @return string
   */
  public function getId(): string {
    return $this->id;
  }

  /**
   * @param string $id
   * @return User
   */
  public function setId(string $id): User {
    $this->id = $id;
    return $this;
  }

  /**
   * @param string[] $properties
   * @return User
   */
  public function setProperties($properties) {
    $this->properties = $properties;
    return $this;
  }

  /**
   * @return string[]
   */
  public function getProperties(): array {
    return $this->properties;
  }
}
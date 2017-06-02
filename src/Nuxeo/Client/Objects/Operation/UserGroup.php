<?php
/**
 * (C) Copyright 2017 Nuxeo SA (http://nuxeo.com/) and contributors.
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

namespace Nuxeo\Client\Objects\Operation;


use JMS\Serializer\Annotation as Serializer;

class UserGroup {

  const className = __CLASS__;

  const USER_TYPE = 'USER_TYPE';

  const GROUP_TYPE = 'GROUP_TYPE';

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $firstName;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $lastName;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $company;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $email;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $username;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $id;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $type;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $prefixed_id;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $displayLabel;

  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  protected $displayIcon;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $description;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $grouplabel;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $groupname;

  /**
   * @var string[]
   * @Serializer\Type("array<string>")
   */
  protected $groups;

  /**
   * @var string[]
   * @Serializer\Type("array<string>")
   */
  protected $subGroups;

  /**
   * @var string[]
   * @Serializer\Type("array<string>")
   */
  protected $parentGroups;

  /**
   * @var string[]
   * @Serializer\Type("array<string>")
   */
  protected $members;

  /**
   * @return string
   */
  public function getFirstName() {
    return $this->firstName;
  }

  /**
   * @return string
   */
  public function getLastName() {
    return $this->lastName;
  }

  /**
   * @return string
   */
  public function getCompany() {
    return $this->company;
  }

  /**
   * @return string
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * @return string
   */
  public function getUsername() {
    return $this->username;
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
  public function getType() {
    return $this->type;
  }

  /**
   * @return string
   */
  public function getPrefixedId() {
    return $this->prefixed_id;
  }

  /**
   * @return string
   */
  public function getDisplayLabel() {
    return $this->displayLabel;
  }

  /**
   * @return bool
   */
  public function isDisplayIcon() {
    return $this->displayIcon;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @return string
   */
  public function getGroupLabel() {
    return $this->grouplabel;
  }

  /**
   * @return string
   */
  public function getGroupName() {
    return $this->groupname;
  }

  /**
   * @return \string[]
   */
  public function getGroups() {
    return $this->groups;
  }

  /**
   * @return \string[]
   */
  public function getSubGroups() {
    return $this->subGroups;
  }

  /**
   * @return \string[]
   */
  public function getParentGroups() {
    return $this->parentGroups;
  }

  /**
   * @return \string[]
   */
  public function getMembers() {
    return $this->members;
  }

}

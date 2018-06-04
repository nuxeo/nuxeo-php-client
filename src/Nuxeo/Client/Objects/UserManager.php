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


use Nuxeo\Client\Objects\User\Group;
use Nuxeo\Client\Objects\User\Groups;
use Nuxeo\Client\Objects\User\User;
use Nuxeo\Client\Objects\User\Users;
use Nuxeo\Client\Spi\Http\Method\GET;
use Nuxeo\Client\Spi\Http\Method\POST;
use Nuxeo\Client\Spi\Http\Method\PUT;
use Nuxeo\Client\Spi\Objects\AbstractConnectable;

class UserManager extends AbstractConnectable {

  /**
   * @return User
   */
  public function fetchCurrentUser() {
    return $this->getNuxeoClient()->automation('login')->execute(User::class);
  }

  /**
   * @param User $user
   * @return User
   */
  public function createUser($user) {
    return $this->getResponseNew(POST::create('user')
      ->setBody($user), User::class);
  }

  /**
   * @param string $query
   * @return Users
   */
  public function searchUser($query) {
    return $this->getResponseNew(GET::create('user/search?q={query}'), Users::class);
  }

  /**
   * @param string $username
   * @param User $user
   * @return User
   */
  public function updateUser($username, $user) {
    return $this->getResponseNew(PUT::create('user/{username}')
      ->setBody($user), User::class);
  }

  /**
   * @param Group $group
   * @return Group
   */
  public function createGroup($group) {
    return $this->getResponseNew(POST::create('group')
      ->setBody($group), Group::class);
  }

  /**
   * @param string $query
   * @return Groups
   */
  public function searchGroup($query) {
    return $this->getResponseNew(GET::create('group/search?q={query}'), Groups::class);
  }

  /**
   * @param string $groupName
   * @param Group $group
   * @return Group
   */
  public function updateGroup($groupName, $group) {
    return $this->getResponseNew(PUT::create('group/{groupName}')
      ->setBody($group), Group::class);
  }

  /**
   * @param string $username
   * @param string $groupName
   * @return User
   */
  public function attachGroupToUser($username, $groupName) {
    return $this->getResponseNew(POST::create('user/{username}/group/{groupName}'), User::class);
  }

  /**
   * @param string $groupName
   * @param string $username
   * @return Group
   */
  public function attachUserToGroup($groupName, $username) {
    return $this->getResponseNew(POST::create('group/{groupName}/user/{username}'), Group::class);
  }

  /**
   * @param string $groupName
   * @return Users
   */
  public function fetchGroupUsers($groupName) {
    return $this->getResponseNew(GET::create('group/{groupName}/@users'), Users::class);
  }

  /**
   * @param string $groupName
   * @return Groups
   */
  public function fetchGroupGroups($groupName) {
    return $this->getResponseNew(GET::create('group/{groupName}/@groups'), Groups::class);
  }
}

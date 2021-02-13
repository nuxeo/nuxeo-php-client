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

namespace Nuxeo\Client\Tests;

use Doctrine\Common\Annotations\AnnotationException;
use Nuxeo\Client\Objects\User\Group;
use Nuxeo\Client\Objects\User\User;
use Nuxeo\Client\Tests\Framework\TestCase;

class UserManagerTest extends TestCase {

  const DEFAULT_USER_LOGIN = 'neo';
  const DEFAULT_USER_PASSWORD = 'passwd';
  const DEFAULT_USER_EMAIL = 'devnull@nuxeo.com';
  const DEFAULT_GROUP_NAME = 'somegroup';

  /**
   * @throws AnnotationException
   */
  public function testCreateUser() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('user.json')));
    $inputUser = $this->createUser();

    $userManager = $this->getClient()->userManager();
    $userManager->createUser($inputUser);

    $request = $this->getClient()->getRequest();

    $this->assertEquals('POST', $request->getMethod());
    $this->assertRequestPathMatches($this->getClient(), 'user');

    /** @var User $user */
    $user = $userManager->getConverter()->readJSON($request->getBody(), User::class);
    $this->assertEquals($inputUser->getUsername(), $user->getUsername());
  }

  public function testSearchUsers() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('users.json')));

    $users = $this->getClient()
      ->userManager()
      ->searchUser('*');

    $request = $this->getClient()->getRequest();

    $this->assertEquals('q=*', $request->getUri()->getQuery());
    $this->assertNotNull($users);
    $this->assertCount(1, $users);
    $this->assertEquals('Administrator', $users[0]->getUsername());
  }

  /**
   * @throws AnnotationException
   */
  public function testUpdateUser() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('users.json')));

    /** @var User $user */
    $userManager = $this->getClient()->userManager();
    $user = $userManager->searchUser('*')[0];

    $this->assertEquals('Skynet', $user->getCompany());

    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('user.json')));
    $user->setCompany('Nuxeo')
      ->save();

    $this->assertEquals('Nuxeo', $user->getCompany());

    $request = $this->getClient()->getRequest();
    $user = $userManager->getConverter()->readJSON($request->getBody(), User::class);

    $this->assertEquals('PUT', $request->getMethod());
    $this->assertEquals('Nuxeo', $user->getCompany());
  }

  /**
   * @throws AnnotationException
   */
  public function testCreateGroup() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('group.json')));
    $inputGroup = $this->createGroup();

    $userManager = $this->getClient()->userManager();
    $userManager->createGroup($inputGroup);

    $request = $this->getClient()->getRequest();

    $this->assertEquals('POST', $request->getMethod());
    $this->assertRequestPathMatches($this->getClient(), 'group');

    /** @var Group $group */
    $group = $userManager->getConverter()->readJSON($request->getBody(), Group::class);
    $this->assertEquals($inputGroup->getGroupName(), $group->getGroupName());
  }

  public function testSearchGroups() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('groups.json')));

    $groups = $this->getClient()
      ->userManager()
      ->searchGroup('*');

    $request = $this->getClient()->getRequest();

    $this->assertEquals('q=*', $request->getUri()->getQuery());
    $this->assertNotNull($groups);
    $this->assertCount(5, $groups);
    $this->assertEquals('administrators', $groups[0]->getGroupName());
  }

  /**
   * @throws AnnotationException
   */
  public function testUpdateGroup() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('groups.json')));

    /** @var Group $group */
    $userManager = $this->getClient()->userManager();
    $group = $userManager->searchGroup('*')[0];

    $this->assertEquals('Administrators group', $group->getGroupLabel());

    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('group.json')));
    $group->setGroupLabel('Admin group')
      ->save();

    $this->assertEquals('Admin group', $group->getGroupLabel());

    $request = $this->getClient()->getRequest();
    $group = $userManager->getConverter()->readJSON($request->getBody(), Group::class);

    $this->assertEquals('PUT', $request->getMethod());
    $this->assertEquals('Admin group', $group->getGroupLabel());
  }

  public function testAttachGroupToUser() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('users.json')));
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('user.json')));

    $this->getClient()
      ->userManager()
      ->searchUser('*')[0]
      ->addGroup('members');

    $this->assertEquals('POST', $this->getClient()->getRequest()->getMethod());
    $this->assertStringMatchesFormat('%s/user/%s/group/members', (string) $this->getClient()->getRequest()->getUri());
  }

  public function testAttachUserToGroup() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('groups.json')));
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('group.json')));

    $this->getClient()
      ->userManager()
      ->searchGroup('*')[0]
      ->addUser('toto');

    $this->assertEquals('POST', $this->getClient()->getRequest()->getMethod());
    $this->assertStringMatchesFormat('%s/group/%s/user/toto', (string) $this->getClient()->getRequest()->getUri());
  }

  public function testFetchUserGroups() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('users.json')));

    $this->assertCount(2, $this->getClient()
      ->userManager()
      ->searchUser('*')[0]
      ->getProperties()['groups']);
  }

  public function testFetchGroupUsers() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('groups.json')));
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('users.json')));

    $this->assertCount(1, $this->getClient()
      ->userManager()
      ->searchGroup('*')[0]
      ->fetchUsers());

    $this->assertEquals('GET', $this->getClient()->getRequest()->getMethod());
    $this->assertStringMatchesFormat('%s/group/%s/@users', (string) $this->getClient()->getRequest()->getUri());
  }

  public function testFetchGroupGroups() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('groups.json')));
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('groups.json')));

    $this->assertCount(5, $this->getClient()
      ->userManager()
      ->searchGroup('*')[0]
      ->fetchGroups());

    $this->assertEquals('GET', $this->getClient()->getRequest()->getMethod());
    $this->assertStringMatchesFormat('%s/group/%s/@groups', (string) $this->getClient()->getRequest()->getUri());
  }

  /**
   * @return User
   */
  protected function createUser() {
    return (new User())
      ->setUsername(self::DEFAULT_USER_LOGIN)
      ->setCompany('Nuxeo')
      ->setEmail(self::DEFAULT_USER_EMAIL)
      ->setFirstName('Thomas A.')
      ->setLastName('Anderson')
      ->setPassword(self::DEFAULT_USER_PASSWORD);
  }

  protected function createGroup() {
    return (new Group())
      ->setGroupName(self::DEFAULT_GROUP_NAME)
      ->setGroupLabel('My Group');
  }

}

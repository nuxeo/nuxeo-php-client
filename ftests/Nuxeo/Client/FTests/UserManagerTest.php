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

namespace Nuxeo\Client\FTests;


use Nuxeo\Client\FTests\Framework\TestCase;
use Nuxeo\Client\Objects\User\Group;
use Nuxeo\Client\Objects\User\User;

/**
 * @group server
 */
class UserManagerTest extends TestCase {

  public function testUsersAndGroups() {
    $userManager = $this->getClient()
      ->userManager();

    $user = (new User())
      ->setUsername('neo_'.md5(microtime()))
      ->setCompany('Nuxeo')
      ->setEmail('devnull@nuxeo.com')
      ->setFirstName('Thomas A.')
      ->setLastName('Anderson')
      ->setPassword('passwd');

    $group = (new Group())
      ->setGroupName('meta_cortex_'.md5(microtime()))
      ->setGroupLabel('Cortex Corp');

    $user = $userManager->createUser($user);
    $user->setCompany('Meta Cortex')
      ->save();

    $group = $userManager->createGroup($group);
    $group->setGroupLabel('Meta Cortex')
      ->save();

    $group->addUser($user->getUsername());
    $user = $user->addGroup('administrators');

    $users = [];
    /** @var User $u */
    foreach($userManager->searchGroup('administrators')[0]->fetchUsers() as $u) {
      $users[] = $u->getUsername();
    }

    $this->assertEquals('Meta Cortex', $user->getCompany());
    $this->assertEquals('Meta Cortex', $group->getGroupLabel());
    $this->assertContains($user->getUsername(), $users);

    $test = $group->getGroupName();
    $test2 = $user->getProperties()['groups'];

    $this->assertContains($test, $test2);
  }

}

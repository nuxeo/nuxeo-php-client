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

namespace Drupal\Tests\nuxeo\Functional;


use Drupal\Tests\BrowserTestBase;
use Symfony\Bundle\WebServerBundle\WebServer;
use Symfony\Bundle\WebServerBundle\WebServerConfig;

class NuxeoPageTest extends BrowserTestBase {

  protected static $modules = ['nuxeo'];

  private $pidFile;

  protected function setUp() {
    $this->pidFile = getcwd() . '/.web-server-pid';

    $docRoot = __DIR__ . '/../../../../..';

    (new WebServer)->start(
      new WebServerConfig($docRoot, 'tests', '127.0.0.1:8888', $docRoot.'/.ht.router.php'),
      $this->pidFile);

    parent::setUp();
  }

  protected function tearDown() {
    unlink($this->pidFile);
    parent::tearDown();
  }


  /**
   * @throws \Behat\Mink\Exception\ExpectationException
   * @throws \Behat\Mink\Exception\ResponseTextException
   */
  public function testNuxeoPage() {
    $this->drupalLogin($this->drupalCreateUser([], 'Toto', true));
    $this->drupalGet('nuxeo');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('myfile.txt');
  }

}

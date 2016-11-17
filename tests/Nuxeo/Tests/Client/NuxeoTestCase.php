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
 * Contributors:
 *     Pierre-Gildas MILLON <pgmillon@nuxeo.com>
 */

namespace Nuxeo\Tests\Client;


use Guzzle\Tests\Http\Server;

abstract class NuxeoTestCase extends \PHPUnit_Framework_TestCase {
  const NEWFILE_NAME = 'myfile.txt';
  const PASSWORD = 'Administrator';
  const NEWFILE_PATH = 'myfile.txt';
  const LOGIN = 'Administrator';
  const MYFILE_CONTENT = 'Hello World';
  const NEWFILE_TYPE = 'text/plain';
  const MYFILE_DOCPATH = '/default-domain/workspaces/MyWorkspace/MyFile';

  /**
   * @var Server
   */
  protected $server;

  public function readPartFromFile($path) {
    $part = file_get_contents($this->getResource($path));
    return str_replace(PHP_EOL, "\r\n", $part);
  }

  protected function setUp() {
    $this->server = new Server();
    $this->server->start();
  }

  protected function tearDown() {
    $this->server->stop();
  }

  /**
   * Get the full path to a file located in the tests resources
   * @param string $relativePath
   * @return string
   */
  public function getResource($relativePath) {
    $file = new \SplFileObject($relativePath, 'rb', true);
    return $file->getRealPath();
  }

}
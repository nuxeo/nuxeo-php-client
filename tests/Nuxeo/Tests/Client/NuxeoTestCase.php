<?php
/**
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the GNU Lesser General Public License
 * (LGPL) version 2.1 which accompanies this distribution, and is available at
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
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
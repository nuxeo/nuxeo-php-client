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

namespace Nuxeo\Client\Tests\Framework;


use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\Response;
use Nuxeo\Client\Tests\Client;

abstract class TestCase extends \PHPUnit_Framework_TestCase {
  const NEWFILE_NAME = 'myfile.txt';
  const PASSWORD = 'Administrator';
  const NEWFILE_PATH = 'myfile.txt';
  const LOGIN = 'Administrator';
  const MYFILE_CONTENT = 'Hello World';
  const NEWFILE_TYPE = 'text/plain';
  const MYFILE_DOCPATH = '/default-domain/workspaces/MyWorkspace/MyFile';
  const URL = 'http://localhost:8080/nuxeo';

  /**
   * @var Client
   */
  private $client;

  public function readPartFromFile($path) {
    $part = file_get_contents($this->getResource($path));
    return str_replace(PHP_EOL, "\r\n", $part);
  }

  protected function tearDown() {
    unset($this->client);
  }

  /**
   * @param string $url
   * @param string $username
   * @param string $password
   * @return Client
   */
  protected function getClient($url = self::URL, $username = self::LOGIN, $password = self::PASSWORD) {
    if(null === $this->client) {
      $this->client = new Client($url, $username, $password);
    }
    return $this->client;
  }

  /**
   * @param int $code
   * @param array $headers
   * @param string $body
   * @return Response
   */
  protected function createResponse($code = 200, $headers = array(), $body = '') {
    $response = new Response($code);
    $response->addHeaders($headers);
    $response->setBody($body);

    return $response;
  }

  /**
   * @param string $jsonContent
   * @return Response
   */
  protected function createJsonResponse($jsonContent) {
    return $this->createResponse(200, array('Content-Type' => Constants::CONTENT_TYPE_JSON), $jsonContent);
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

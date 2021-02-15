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

namespace Nuxeo\Client\Tests;


use Nuxeo\Client\Objects\NuxeoVersion;
use Nuxeo\Client\Request;
use Nuxeo\Client\Tests\Framework\TestCase;
use Nuxeo\Client\Tests\Util\ArrayIterator;

class BaseTest extends TestCase {

  public function testFetchNuxeoVersion() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('cmis.json')));

    $version = $this->getClient()->getServerVersion();

    self::assertNotNull($version);
    self::assertTrue($version->gte(NuxeoVersion::$LTS_2015));
  }

  public function testFetchCurrentUser() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('login.json')));

    self::assertEquals('Administrator', $this->getClient()->connect()->getUsername());
  }

  public function testGetRequest() {
    $client = $this->getClient()
      ->addResponse($this->createResponse());

    $client->get('/');

    self::assertCount(1, $requests = $client->getRequests());

    /** @var Request $request */
    [$request] = $requests;

    self::assertNotFalse($authorization = $request->getHeaderLine('Authorization'));

    [$username, $password] = explode(':', base64_decode(ArrayIterator::fromArray(explode(
      ' ', $authorization))->offsetGet(1)));

    self::assertEquals(self::LOGIN, $username);
    self::assertEquals(self::PASSWORD, $password);
  }

  public function testSimpleInterceptor() {
    $headerName = 'NX_RANDOM';
    $headerValue = 'MyValue';

    $client = $this->getClient()
      ->addResponse($this->createResponse());

    $client
      ->header($headerName, $headerValue)
      ->get('/');

    self::assertCount(1, $requests = $client->getRequests());

    /** @var Request $request */
    [$request] = $requests;

    self::assertEquals($headerValue, $request->getHeaderLine($headerName));
  }

}

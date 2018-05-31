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


use GuzzleHttp\Exception\ClientException;
use Nuxeo\Client\Auth\PortalSSOAuthentication;
use Nuxeo\Client\Auth\TokenAuthentication;
use Nuxeo\Client\Constants;
use Nuxeo\Client\Objects\NuxeoVersion;
use Nuxeo\Client\Request;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Tests\Framework\TestCase;
use Nuxeo\Client\Tests\Util\ArrayIterator;

class BaseTest extends TestCase {

  const TOKEN_APP_NAME = 'myApplication';
  const TOKEN_DEVICE = 'myDevice';

  public function testFetchNuxeoVersion() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile($this->getResource('cmis.json')));

    $version = $this->getClient()->getServerVersion();

    $this->assertNotNull($version);
    $this->assertTrue($version->gte(NuxeoVersion::$LTS_2015));
  }

  public function testGetRequest() {
    $client = $this->getClient()
      ->addResponse($this->createResponse());

    $client->get('/');

    $this->assertCount(1, $requests = $client->getRequests());

    /** @var Request $request */
    list($request) = $requests;

    $this->assertNotFalse($authorization = $request->getHeaderLine('Authorization'));

    list($username, $password) = explode(':', base64_decode(ArrayIterator::fromArray(explode(
      ' ', $authorization))->offsetGet(1)));

    $this->assertEquals(self::LOGIN, $username);
    $this->assertEquals(self::PASSWORD, $password);
  }

  public function testPortalSSOAuth() {
    $client = $this->getClient()
      ->addResponse($this->createResponse());

    $client
      ->setAuthenticationMethod(new PortalSSOAuthentication('secret', self::LOGIN))
      ->get('/');

    $this->assertCount(1, $requests = $client->getRequests());

    /** @var \Nuxeo\Client\Request $request */
    list($request) = $requests;

    $this->assertFalse($request->hasHeader('Authorization'));
    $this->assertNotFalse($request->hasHeader(PortalSSOAuthentication::NX_TS));
    $this->assertNotFalse($request->hasHeader(PortalSSOAuthentication::NX_RD));
    $this->assertNotFalse($request->hasHeader(PortalSSOAuthentication::NX_TOKEN));
    $this->assertNotFalse($request->hasHeader(PortalSSOAuthentication::NX_USER));
  }

  public function testTokenAuthentication() {
    $auth_token = 'some_token';
    $client = $this->getClient()
      ->addResponse($this->createResponse());

    $client
      ->setAuthenticationMethod(new TokenAuthentication($auth_token))
      ->get('/');

    $this->assertCount(1, $requests = $client->getRequests());

    /** @var \Nuxeo\Client\Request $request */
    list($request) = $requests;

    $this->assertFalse($request->hasHeader('Authorization'));
    $this->assertEquals($auth_token, $request->getHeaderLine(TokenAuthentication::HEADER_TOKEN));
  }

  /**
   * @expectedException \Nuxeo\Client\Spi\NuxeoClientException
   */
  public function testUnauthorized() {
    $client = $this->getClient(self::URL, self::LOGIN, null)
      ->addResponse($this->createResponse(401));

    try {
      $client->get('/');
    } catch(NuxeoClientException $e) {
      /** @var ClientException $previous */
      $this->assertInstanceOf(ClientException::class, $previous = $e->getPrevious());
      $this->assertEquals(401, $previous->getResponse()->getStatusCode());
      throw $e;
    }
    $this->fail('Should be unauthorized');
  }

  public function testRequestAuthenticationToken() {
    $token = 'some_token';
    $client = $this->getClient()
      ->addResponse($this->createResponse(200, array(), $token));

    $this->assertEquals($token, $client->requestAuthenticationToken(self::TOKEN_APP_NAME, self::TOKEN_DEVICE));
    $this->assertCount(1, $requests = $client->getRequests());

    /** @var \Nuxeo\Client\Request $request */
    list($request) = $requests;

    $this->assertEquals('authentication/token', $request->getUri()->getPath());

    $queryParams = [];
    parse_str($request->getUri()->getQuery(), $queryParams);

    $this->assertArraySubset(array(
        'applicationName' => self::TOKEN_APP_NAME,
        'deviceId' => self::TOKEN_DEVICE,
        'deviceDescription' => '',
        'permission' => Constants::SECURITY_READ_WRITE,
        'revoke' => false
    ), $queryParams);
  }

}

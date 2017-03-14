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


use Nuxeo\Client\Api\Auth\PortalSSOAuthentication;
use Nuxeo\Client\Api\Auth\TokenAuthentication;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\Request;
use Nuxeo\Client\Tests\Framework\TestCase;
use Nuxeo\Client\Tests\Util\ArrayIterator;

class BaseTest extends TestCase {

  const TOKEN_APP_NAME = 'myApplication';
  const TOKEN_DEVICE = 'myDevice';

  public function testGetRequest() {
    $client = $this->getClient()
      ->addResponse($this->createResponse());

    $client->get('/');

    $this->assertCount(1, $requests = $client->getRequests());

    /** @var Request $request */
    list($request) = $requests;

    $this->assertNotFalse($authorization = $request->getHeader('Authorization'));

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

    /** @var \Nuxeo\Client\Api\Request $request */
    list($request) = $requests;

    $this->assertFalse($request->hasHeader('Authorization'));
    $this->assertNotFalse($request->getHeader(PortalSSOAuthentication::NX_TS));
    $this->assertNotFalse($request->getHeader(PortalSSOAuthentication::NX_RD));
    $this->assertNotFalse($request->getHeader(PortalSSOAuthentication::NX_TOKEN));
    $this->assertNotFalse($request->getHeader(PortalSSOAuthentication::NX_USER));
  }

  public function testTokenAuthentication() {
    $auth_token = 'some_token';
    $client = $this->getClient()
      ->addResponse($this->createResponse());

    $client
      ->setAuthenticationMethod(new TokenAuthentication($auth_token))
      ->get('/');

    $this->assertCount(1, $requests = $client->getRequests());

    /** @var \Nuxeo\Client\Api\Request $request */
    list($request) = $requests;

    $this->assertFalse($request->hasHeader('Authorization'));
    $this->assertEquals($auth_token, $request->getHeader(TokenAuthentication::HEADER_TOKEN));
  }

  /**
   * @expectedException \Nuxeo\Client\Internals\Spi\NuxeoClientException
   */
  public function testUnauthorized() {
    $client = $this->getClient(self::URL, self::LOGIN, null)
      ->addResponse($this->createResponse(401, array(), 'Unauthorized'));

    $client->get('/');

    $this->assertCount(1, $client->getRequests());
  }

  public function testRequestAuthenticationToken() {
    $token = 'some_token';
    $client = $this->getClient()
      ->addResponse($this->createResponse(200, array(), $token));

    $this->assertEquals($token, $client->requestAuthenticationToken(self::TOKEN_APP_NAME, self::TOKEN_DEVICE));
    $this->assertCount(1, $requests = $client->getRequests());

    /** @var \Nuxeo\Client\Api\Request $request */
    list($request) = $requests;

    $this->assertEquals('authentication/token', $request->getUrl(true)->getPath());
    $this->assertArraySubset(array(
        'applicationName' => self::TOKEN_APP_NAME,
        'deviceId' => self::TOKEN_DEVICE,
        'deviceDescription' => '',
        'permission' => Constants::SECURITY_READ_WRITE,
        'revoke' => false
    ), $request->getUrl(true)->getQuery()->toArray());
  }

}

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


use Guzzle\Http\Message\Response;
use Nuxeo\Client\Api\Auth\PortalSSOAuthentication;
use Nuxeo\Client\Api\Auth\TokenAuthentication;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\NuxeoClient;
use Nuxeo\Client\Api\Utils\ArrayIterator;
use Nuxeo\Client\Internals\Spi\Http\EntityEnclosingRequest;

class BaseTest extends NuxeoTestCase {

  const TOKEN_APP_NAME = 'myApplication';
  const TOKEN_DEVICE = 'myDevice';

  public function testGetRequest() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200)
    ));

    $client->get('/');

    $this->assertCount(1, $requests = $this->server->getReceivedRequests(true));

    /** @var EntityEnclosingRequest $request */
    list($request) = $requests;

    $this->assertTrue($request->hasHeader('Authorization'));

    list($username, $password) = explode(':', base64_decode(ArrayIterator::fromArray(explode(
      ' ', $request->getHeader('Authorization')->getIterator()->current()))->offsetGet(1)));

    $this->assertEquals(self::LOGIN, $username);
    $this->assertEquals(self::PASSWORD, $password);
  }

  public function testPortalSSOAuth() {
    $client = new NuxeoClient($this->server->getUrl());

    $this->server->enqueue(array(
      new Response(200)
    ));

    $client
      ->setAuthenticationMethod(new PortalSSOAuthentication('secret', self::LOGIN))
      ->get('/');

    $requests = $this->server->getReceivedRequests(true);

    /** @var EntityEnclosingRequest $request */
    list($request) = $requests;

    $this->assertFalse($request->hasHeader('Authentication'));
    $this->assertTrue($request->hasHeader(PortalSSOAuthentication::NX_TS));
    $this->assertTrue($request->hasHeader(PortalSSOAuthentication::NX_RD));
    $this->assertTrue($request->hasHeader(PortalSSOAuthentication::NX_TOKEN));
    $this->assertTrue($request->hasHeader(PortalSSOAuthentication::NX_USER));
  }

  public function testTokenAuthentication() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, self::PASSWORD);
    $auth_token = 'some_token';

    $this->server->enqueue(array(
      new Response(200)
    ));

    $client
      ->setAuthenticationMethod(new TokenAuthentication($auth_token))
      ->get('/');

    $requests = $this->server->getReceivedRequests(true);

    /** @var EntityEnclosingRequest $request */
    list($request) = $requests;

    $this->assertFalse($request->hasHeader('Authentication'));
    $this->assertTrue($request->hasHeader(TokenAuthentication::HEADER_TOKEN));
    $this->assertEquals($auth_token, $request->getHeader(TokenAuthentication::HEADER_TOKEN));
  }

  /**
   * @expectedException \Nuxeo\Client\Internals\Spi\NuxeoClientException
   */
  public function testUnauthorized() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, null);

    $this->server->enqueue(array(
      new Response(401, null, 'Unauthorized')
    ));

    $client->get('/');

    $this->assertCount(1, $this->server->getReceivedRequests());
  }

  public function testRequestAuthenticationToken() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, self::PASSWORD);

    $token = 'some_token';
    $this->server->enqueue(array(
      new Response(200, null, $token)
    ));

    $this->assertEquals($token, $client->requestAuthenticationToken(self::TOKEN_APP_NAME, self::TOKEN_DEVICE));
    $this->assertCount(1, $requests = $this->server->getReceivedRequests(true));

    /** @var EntityEnclosingRequest $request */
    list($request) = $requests;

    $this->assertEquals('/authentication/token', $request->getPath());
    $this->assertArraySubset(array(
        'applicationName' => self::TOKEN_APP_NAME,
        'deviceId' => self::TOKEN_DEVICE,
        'deviceDescription' => '',
        'permission' => Constants::SECURITY_READ_WRITE,
        'revoke' => false
    ), $request->getQuery()->toArray());
  }

}

<?php
/*
 * (C) Copyright 2021 Nuxeo SA (http://nuxeo.com/) and contributors.
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


use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use GuzzleHttp\Exception\ClientException;
use Nuxeo\Client\Auth\OAuth2Authentication;
use Nuxeo\Client\Auth\PortalSSOAuthentication;
use Nuxeo\Client\Auth\TokenAuthentication;
use Nuxeo\Client\Constants;
use Nuxeo\Client\Request;
use Nuxeo\Client\Spi\NuxeoClientException;

class AuthTest extends Framework\TestCase {

  use ArraySubsetAsserts;

  public const TOKEN_APP_NAME = 'myApplication';
  public const TOKEN_DEVICE = 'myDevice';

  public function testUnauthorized() {
    $this->expectException(NuxeoClientException::class);

    $client = $this->getClient(self::URL, self::LOGIN, null)
      ->addResponse($this->createResponse(401));

    try {
      $client->get('/');
    } catch(NuxeoClientException $e) {
      /** @var ClientException $previous */
      self::assertInstanceOf(ClientException::class, $previous = $e->getPrevious());
      self::assertEquals(401, $previous->getResponse()->getStatusCode());
      throw $e;
    }
    self::fail('Should be unauthorized');
  }

  public function testPortalSSOAuth() {
    $client = $this->getClient()
      ->addResponse($this->createResponse());

    $client
      ->setAuthenticationMethod(new PortalSSOAuthentication('secret', self::LOGIN))
      ->get('/');

    self::assertCount(1, $requests = $client->getRequests());

    /** @var Request $request */
    [$request] = $requests;

    self::assertFalse($request->hasHeader('Authorization'));
    self::assertNotFalse($request->hasHeader(PortalSSOAuthentication::NX_TS));
    self::assertNotFalse($request->hasHeader(PortalSSOAuthentication::NX_RD));
    self::assertNotFalse($request->hasHeader(PortalSSOAuthentication::NX_TOKEN));
    self::assertNotFalse($request->hasHeader(PortalSSOAuthentication::NX_USER));
  }

  public function testTokenAuthentication() {
    $auth_token = 'some_token';
    $client = $this->getClient()
      ->addResponse($this->createResponse());

    $client
      ->setAuthenticationMethod(new TokenAuthentication($auth_token))
      ->get('/');

    self::assertCount(1, $requests = $client->getRequests());

    /** @var Request $request */
    [$request] = $requests;

    self::assertFalse($request->hasHeader('Authorization'));
    self::assertEquals($auth_token, $request->getHeaderLine(TokenAuthentication::HEADER_TOKEN));
  }

  public function testRequestAuthenticationToken() {
    $token = 'some_token';
    $client = $this->getClient()
      ->addResponse($this->createResponse(200, array(), $token));

    self::assertEquals($token, $client->requestAuthenticationToken(self::TOKEN_APP_NAME, self::TOKEN_DEVICE));
    self::assertCount(1, $requests = $client->getRequests());

    /** @var Request $request */
    [$request] = $requests;

    self::assertEquals('authentication/token', $request->getUri()->getPath());

    $queryParams = [];
    parse_str($request->getUri()->getQuery(), $queryParams);

    self::assertArraySubset(array(
      'applicationName' => self::TOKEN_APP_NAME,
      'deviceId' => self::TOKEN_DEVICE,
      'deviceDescription' => '',
      'permission' => Constants::SECURITY_READ_WRITE,
      'revoke' => false
    ), $queryParams);
  }

  public function testOAuth2Authentication() {
    $accessToken = 'some_token';
    $client = $this->getClient()
      ->withAuthentication(new OAuth2Authentication($accessToken))
      ->addResponse($this->createResponse());

    $client->get('/');

    /** @var Request $request */
    [$request] = $client->getRequests();

    self::assertEquals(
      OAuth2Authentication::HEADER_VALUE_PREFIX.' '.$accessToken,
      $request->getHeaderLine(OAuth2Authentication::HEADER_TOKEN));
  }

}

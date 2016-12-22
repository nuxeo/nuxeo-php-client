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

use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Http\Message\Response;
use Nuxeo\Client\Api\Auth\PortalSSOAuthentication;
use Nuxeo\Client\Api\Auth\TokenAuthentication;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\NuxeoClient;
use Nuxeo\Client\Api\Objects\Blob;
use Nuxeo\Client\Api\Objects\DirectoryEntries;
use Nuxeo\Client\Api\Objects\Document;
use Nuxeo\Client\Api\Objects\Documents;
use Nuxeo\Client\Api\Utils\ArrayIterator;

class TestNuxeoClient extends NuxeoTestCase {

  public function testGetRequest() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200, array('Content-Type' => Constants::CONTENT_TYPE_JSON), file_get_contents($this->getResource('user.json')))
    ));

    $userDoc = $client->automation()->execute(Document::className, 'User.Get');

    $requests = $this->server->getReceivedRequests(true);

    $this->assertCount(1, $requests);

    /** @var EntityEnclosingRequest $request */
    list($request) = $requests;

    $this->assertTrue($request->hasHeader('Authorization'));

    list($username, $password) = explode(':', base64_decode(ArrayIterator::fromArray(explode(
      ' ', $request->getHeader('Authorization')->getIterator()->current()))->offsetGet(1)));

    $this->assertEquals(self::LOGIN, $username);
    $this->assertEquals(self::PASSWORD, $password);

    $this->assertInstanceOf(Document::className, $userDoc);
    $this->assertEquals(self::LOGIN, $userDoc->getUid());
  }

  public function testPortalSSOAuth() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200, array('Content-Type' => Constants::CONTENT_TYPE_JSON), file_get_contents($this->getResource('user.json')))
    ));

    $client
      ->setAuthenticationMethod(new PortalSSOAuthentication('secret', self::LOGIN))
      ->automation()
      ->execute(Document::className, 'User.Get');

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
      new Response(200, array('Content-Type' => Constants::CONTENT_TYPE_JSON), file_get_contents($this->getResource('user.json')))
    ));

    $client
      ->setAuthenticationMethod(new TokenAuthentication($auth_token))
      ->automation()
      ->execute(Document::className, 'User.Get');

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
      new Response(401, array('Content-Type' => Constants::CONTENT_TYPE_JSON), 'Unauthorized')
    ));

    $client->automation()->execute(Document::className, 'Document.Query');

    $this->assertCount(1, $this->server->getReceivedRequests());
  }

  public function testListDocuments() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200, array('Content-Type' => Constants::CONTENT_TYPE_JSON), file_get_contents($this->getResource('document-list.json')))
    ));

    /** @var Documents $documents */
    $documents = $client
      ->schemas('*')
      ->automation()
      ->param('query', 'SELECT * FROM Document')
      ->execute(Documents::className, 'Document.Query');

    $this->assertInstanceOf(Documents::className, $documents);
    $this->assertEquals(5, $documents->size());

    foreach ($documents->getDocuments() as $document) {
      $this->assertNotNull($document->getUid());
      $this->assertNotNull($document->getPath());
      $this->assertNotNull($document->getType());
      $this->assertNotNull($document->getState());
      $this->assertNotNull($document->getTitle());
      $this->assertNotNull($document->getProperty('dc:created'));
    }

    $domain = $documents->getDocument(0);
    $this->assertNotNull($domain);
    $this->assertEquals('Domain', $domain->getType());
    $this->assertEquals('Domain', $domain->getProperty('dc:title'));
    $this->assertNull($domain->getProperty('dc:nonexistent'));
  }

  public function testGetBlob() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200, null, self::MYFILE_CONTENT)
    ));

    /** @var Blob $blob */
    $blob = $client->automation('Blob.Get')
      ->input(self::MYFILE_DOCPATH)
      ->execute(Blob::className);

    /** @var EntityEnclosingRequestInterface $request */
    list($request) = $this->server->getReceivedRequests(true);

    $this->assertEquals(sprintf('{"params":{},"input":"%s"}', self::MYFILE_DOCPATH), (string) $request->getBody());
    $this->assertEquals(self::MYFILE_CONTENT, file_get_contents($blob->getFile()->getPathname()));
  }

  /**
   * @expectedException \Nuxeo\Client\Internals\Spi\NuxeoClientException
   */
  public function testCannotLoadBlob() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200, null, null)
    ));

    $client->automation('Blob.Attach')->input(Blob::fromFile('/void', null))->execute(Blob::className);

    $this->assertCount(0, $this->server->getReceivedRequests());
  }

  public function testLoadBlob() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200)
    ));

    $client->automation('Blob.AttachOnDocument')
      ->param('document', self::MYFILE_DOCPATH)
      ->input(Blob::fromFile($this->getResource('user.json'), null))
      ->execute(Blob::className);

    $requests = $this->server->getReceivedRequests(true);

    $this->assertCount(1, $requests);

    /** @var EntityEnclosingRequest $request */
    list($request) = $requests;

    $this->assertArrayHasKey('content-type', $request->getHeaders());
    $this->assertStringMatchesFormat(
      'multipart/related;boundary=%s',
      $request->getHeader('content-type')->__toString());

  }

  public function testDirectoryEntries() {
    $client = new NuxeoClient($this->server->getUrl(), self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200, array('Content-Type' => Constants::CONTENT_TYPE_JSON), file_get_contents($this->getResource('directory-entries.json')))
    ));

    $continents = $client->automation('Directory.Entries')
      ->param('directoryName', 'continent')
      ->execute(DirectoryEntries::className);

    $this->assertCount(1, $this->server->getReceivedRequests(true));

    $this->assertInstanceOf(DirectoryEntries::className, $continents);
    $this->assertCount(7, $continents);
    $this->server->flush();

    $ids = array('id001', 'id002', 'id003', 'id004');
    $this->server->enqueue(array(
      new Response(200, array('Content-Type' => Constants::CONTENT_TYPE_JSON), json_encode($ids))
    ));

    $continents = $client->automation('Directory.CreateEntries')
      ->param('directoryName', 'continent')
      ->param('entries', $client->getConverter()->writeJSON(DirectoryEntries::fromArray(array(
        array('id' => 'id001', 'label' => 'label.continent.one'),
        array('id' => 'id002', 'label' => 'label.continent.two', 'ordering' => 42),
        array('id' => 'id003', 'label' => 'label.continent.three', 'obsolete' => 1),
        array('id' => 'id004', 'label' => 'label.continent.four', 'ordering' => 666, 'obsolete' => 5),
      ))))
      ->execute();

    $this->assertCount(1, $requests = $this->server->getReceivedRequests(true));

    /** @var EntityEnclosingRequestInterface $request */
    list($request) = $requests;

    $this->assertNotNull($decoded = json_decode((string) $request->getBody(), true));
    $this->assertTrue(!empty($decoded['params']['entries']) && is_string($decoded['params']['entries']));
    $this->assertTrue(null !== ($entries = json_decode($decoded['params']['entries'], true)) && !empty($entries[0]['id']));
    $this->assertEquals('id001', $entries[0]['id']);
    $this->assertEquals('label.continent.one', $entries[0]['label']);
    $this->assertEquals(42, $entries[1]['ordering']);
    $this->assertEquals(5, $entries[3]['obsolete']);
    $this->assertEquals($ids, $continents);
  }

}
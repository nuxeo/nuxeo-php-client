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
use Guzzle\Http\Message\Response;
use Nuxeo\Automation\Client\NuxeoPhpAutomationClient;

class TestNuxeoClientCompat extends NuxeoTestCase  {


  public function testGetRequest() {
    $client = new NuxeoPhpAutomationClient($this->server->getUrl());
    $session = $client->getSession(self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200, null, file_get_contents('user.json', FILE_USE_INCLUDE_PATH))
    ));

    $request = $session->newRequest('User.Get');
    $this->assertNotNull($request);
    $answer = $request->sendRequest();
    $userDoc = $answer->getDocument(0);
    $this->assertNotNull($userDoc);
    $this->assertEquals(self::LOGIN, $userDoc->getUid());
  }

  /**
   * @expectedException \Nuxeo\Automation\Client\Internals\NuxeoClientException
   */
  public function testUnauthorized() {
    $client = new NuxeoPhpAutomationClient($this->server->getUrl());
    $session = $client->getSession(self::LOGIN, null);

    $this->server->enqueue(array(
      new Response(401, null, 'Unauthorized')
    ));

    $session->newRequest('Document.Query')->sendRequest();

    $this->assertCount(1, $this->server->getReceivedRequests());
  }

  public function testListDocuments() {
    $client = new NuxeoPhpAutomationClient($this->server->getUrl());
    $session = $client->getSession(self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200, null, file_get_contents('document-list.json', FILE_USE_INCLUDE_PATH))
    ));

    $answer = $session->newRequest('Document.Query')
      ->set('params', 'query', 'SELECT * FROM Document')
      ->setSchema('*')
      ->sendRequest();

    $documentsArray = $answer->getDocumentList();

    $this->assertCount(5, $documentsArray);

    foreach ($documentsArray as $document) {
      $this->assertNotNull($document->getUid());
      $this->assertNotNull($document->getPath());
      $this->assertNotNull($document->getType());
      $this->assertNotNull($document->getState());
      $this->assertNotNull($document->getTitle());
      $this->assertNotNull($document->getProperty('dc:created'));
    }

    $domain = $answer->getDocument(0);
    $this->assertNotNull($domain);
    $this->assertEquals('Domain', $domain->getType());
    $this->assertEquals('Domain', $domain->getProperty('dc:title'));
    $this->assertNull($domain->getProperty('dc:nonexistent'));

  }

  public function testGetBlob() {
    $client = new NuxeoPhpAutomationClient($this->server->getUrl());
    $session = $client->getSession(self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200, null, self::MYFILE_CONTENT)
    ));

    $answer = $session->newRequest('Blob.Get')
      ->set('input', 'doc:'.self::MYFILE_DOCPATH)
      ->sendRequest();

    $this->assertEquals(self::MYFILE_CONTENT, $answer);

  }

  /**
   * @expectedException \Nuxeo\Automation\Client\Internals\NuxeoClientException
   */
  public function testCannotLoadBlob() {
    $client = new NuxeoPhpAutomationClient($this->server->getUrl());
    $session = $client->getSession(self::LOGIN, self::PASSWORD);

    $session->newRequest('Blob.Attach')->loadBlob('void');

    $this->assertCount(0, $this->server->getReceivedRequests());
  }

  public function testLoadBlob() {
    $client = new NuxeoPhpAutomationClient($this->server->getUrl());
    $session = $client->getSession(self::LOGIN, self::PASSWORD);

    $request = $session->newRequest('Blob.Attach')
      ->set('params', 'document', array(
        'entity-type' => 'string',
        'value' => self::MYFILE_DOCPATH
      ))
      ->loadBlob($this->getResource(self::NEWFILE_PATH), self::NEWFILE_TYPE);

    $blobList = $request->getBlobList();

    $this->assertTrue(is_array($blobList));
    $this->assertCount(1, $blobList);

    $blob = $blobList[0];

    $this->assertTrue(is_array($blob));
    $this->assertCount(3, $blob);
    $this->assertEquals(self::NEWFILE_NAME, $blob[0]);
    $this->assertEquals(self::NEWFILE_TYPE, $blob[1]);
    $this->assertEquals(file_get_contents($this->getResource(self::NEWFILE_PATH)), $blob[2]);
  }

  public function testAttachBlob() {
    $client = new NuxeoPhpAutomationClient($this->server->getUrl());
    $session = $client->getSession(self::LOGIN, self::PASSWORD);

    $this->server->enqueue(array(
      new Response(200)
    ));

    $session->newRequest('Blob.Attach')
      ->set('params', 'document', array(
        'entity-type' => 'string',
        'value' => self::MYFILE_DOCPATH
      ))
      ->loadBlob($this->getResource(self::NEWFILE_PATH), self::NEWFILE_TYPE)
      ->sendRequest();

    $requests = $this->server->getReceivedRequests(true);

    $this->assertCount(1, $requests);

    /** @var EntityEnclosingRequest $request */
    $request = $requests[0];

    $this->assertArrayHasKey('content-type', $request->getHeaders());
    $this->assertStringMatchesFormat(
      'multipart/related;boundary=%s',
      $request->getHeader('content-type')->__toString());

    $this->assertArrayHasKey('x-nxvoidoperation', $request->getHeaders());
    $this->assertEquals('true', $request->getHeader('x-nxvoidoperation')->__toString());

//    $this->assertArrayHasKey('accept', $request->getHeaders());
//    $this->assertEquals('application/json+nxentity, */*', $request->getHeader('accept')->__toString());

    $this->assertContains($this->readPartFromFile('setblob-part1.txt'), $request->getBody()->__toString());
    $this->assertContains($this->readPartFromFile('setblob-part2.txt'), $request->getBody()->__toString());
  }

}
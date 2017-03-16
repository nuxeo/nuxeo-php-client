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


use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\Objects;
use Nuxeo\Client\Api\Request;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;
use Nuxeo\Client\Internals\Spi\NuxeoException;
use Nuxeo\Client\Tests\Framework\TestCase;
use Nuxeo\Client\Tests\Objects\MyDocType;

class RepositoryTest extends TestCase {
  /**
   * @param Client $client
   * @param string $path
   * @param Objects\Document $document
   * @param string $type
   * @param string $entityType
   * @param int $requestIndex
   */
  protected function assertFetched($client, $path, $document, $type, $entityType, $requestIndex = 0) {
    $this->assertRequestPathMatches($client, $path, $requestIndex);
    $this->assertInstanceOf(Objects\Document::className, $document);
    $this->assertEquals($type, $document->getType());
    $this->assertEquals($entityType, $document->getEntityType());
  }

  public function testFetchDocumentRoot() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('documentRoot.json'));

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentRoot();

    $this->assertFetched($client, 'path', $document, 'Root', 'document');
    $this->assertEquals('/', $document->getPath());
  }

  public function testFetchDocumentRootWithRepositoryName() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('documentRoot.json'));

    $repositoryName = self::DOC_REPOSITORY;

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentRoot(self::DOC_REPOSITORY);

    $this->assertFetched($client, "repo/${repositoryName}/path", $document, 'Root', 'document');
    $this->assertEquals($repositoryName, $document->getRepositoryName());
    $this->assertEquals('/', $document->getPath());
  }

  public function testFetchDocumentByPath() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentByPath(self::DOC_PATH);

    $this->assertFetched($client, 'path'.self::DOC_PATH, $document, self::DOC_TYPE, 'document');
    $this->assertEquals(self::DOC_PATH, $document->getPath());
  }

  public function testFetchDocumentByPathWithRepositoryName() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    $repositoryName = self::DOC_REPOSITORY;

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentByPathWithRepositoryName(self::DOC_PATH, self::DOC_REPOSITORY);

    $this->assertFetched($client, "repo/${repositoryName}/path".self::DOC_PATH, $document, self::DOC_TYPE, 'document');
    $this->assertEquals($repositoryName, $document->getRepositoryName());
    $this->assertEquals(self::DOC_PATH, $document->getPath());
  }

  public function testFetchDocumentById() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentById(self::DOC_UID);

    $this->assertFetched($client, 'id/'.self::DOC_UID, $document, 'Note', 'document');
    $this->assertEquals(self::DOC_UID, $document->getUid());
  }

  public function testFetchDocumentByIdWithRepositoryName() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    $uid = self::DOC_UID;
    $repositoryName = self::DOC_REPOSITORY;

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentByIdWithRepositoryName($uid, 'default');

    $this->assertFetched($client, "repo/${repositoryName}/id/".$uid, $document, 'Note', 'document');
    $this->assertEquals($repositoryName, $document->getRepositoryName());
    $this->assertEquals($uid, $document->getUid());
  }

  public function testCreateDocument() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    $parentPath = self::DOC_PARENT_PATH;

    $parent = Objects\Document::create()
      ->setTitle(self::DOC_TITLE)
      ->setType(self::DOC_TYPE)
      ->setProperty('dc:title', self::DOC_TITLE);

    /** @var Objects\Document $document */
    $document = $client->repository()->createDocumentByPath($parentPath, $parent);

    $this->assertEquals('POST', $client->getRequest()->getMethod());
    $this->assertNotNull($decoded = json_decode((string) $client->getRequest()->getBody(), true));
    $this->assertArraySubset(array(
      'entity-type' => 'document',
      'type' => self::DOC_TYPE,
      'title' => self::DOC_TITLE,
      'properties' => array(
        'dc:title' => self::DOC_TITLE
      )
    ), $decoded);

    $this->assertFetched($client, "path${parentPath}", $document, self::DOC_TYPE, 'document');
    $this->assertEquals(self::DOC_TITLE, $document->getProperty('dc:title'));
  }

  public function testUpdateDocument() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document.json'))
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    $uid = self::DOC_UID;
    $path = self::DOC_PATH;

    $document = Objects\Document::create()
      ->setUid($uid)
      ->setPath($path)
      ->setTitle(self::DOC_TITLE)
      ->setType(self::DOC_TYPE)
      ->setProperty('dc:title', self::DOC_TITLE);

    /** @var Objects\Document $updated */
    $updated = $client->repository()->updateDocument($document);

    $this->assertEquals('PUT', $client->getRequest()->getMethod());
    $this->assertFetched($client, "id/${uid}", $updated, $document->getType(), 'document');
    $this->assertEquals($document->getProperty('dc:title'), $updated->getProperty('dc:title'));

    /** @var Objects\Document $updated */
    $updated = $client->repository()->updateDocumentByPath($path, $document);

    $this->assertEquals('PUT', $client->getRequest()->getMethod());
    $this->assertFetched($client, "path${path}", $updated, $document->getType(), 'document', 1);
    $this->assertEquals($document->getProperty('dc:title'), $updated->getProperty('dc:title'));
  }

  public function testDeleteDocument() {
    $client = $this->getClient()
      ->addResponse($this->createResponse(204))
      ->addResponse($this->createResponse(204));

    $uid = self::DOC_UID;
    $path = self::DOC_PATH;

    $document = Objects\Document::create()
      ->setUid($uid)
      ->setPath($path);

    $client->repository()->deleteDocument($document);
    $client->repository()->deleteDocumentByPath($path);

    /** @var Request $request */
    foreach($client->getRequests() as $request) {
      $this->assertEquals('DELETE', $request->getMethod());
    }

    $this->assertRequestPathMatches($client, "id/${uid}");
    $this->assertRequestPathMatches($client, "path${path}", 1);
  }

  public function testQuery() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document-list.json'));

    /** @var Objects\Documents $documents */
    $documents = $client->repository()->query('SELECT * FROM Document');

    $this->assertRequestPathMatches($client, 'query');
    $this->assertEquals('GET', $client->getRequest()->getMethod());

    $this->assertEquals('documents', $documents->getEntityType());
    $this->assertInstanceOf(Objects\Documents::className, $documents);
    $this->assertCount(5, $documents->getDocuments());
    $this->assertEquals(34, $documents->getTotalSize());

    /** @var Objects\Document $document */
    $this->assertInstanceOf(Objects\Document::className, $document = $documents->getDocument());

    $this->assertEquals(self::DOC_UID, $document->getUid());
    $this->assertEquals(self::DOC_TYPE, $document->getType());
  }

  public function testMarshalling() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    /** @var MyDocType $document */
    $document = $client->repository()->fetchDocumentById(self::DOC_UID, null, MyDocType::className);
    $this->assertInstanceOf(MyDocType::className, $document);
    $this->assertNotNull($document->getCreatedAt());
  }

  /**
   * @expectedException \Nuxeo\Client\Internals\Spi\NuxeoClientException
   */
  public function testFail() {
    $client = $this->getClient()->addResponse($this->createResponse(404));
    $client->repository()->fetchDocumentById('404');
    $this->fail('Should be not found');
  }

  /**
   * @expectedException \Nuxeo\Client\Internals\Spi\NuxeoClientException
   */
  public function testServerError() {
    $client = $this->getClient(self::URL, self::LOGIN, null)
      ->addResponse($this->createResponse(
        500,
        array('Content-Type' => Constants::CONTENT_TYPE_JSON),
        file_get_contents($this->getResource('exception.json'))
      ));

    try {
      $client->repository()->fetchDocumentById('404');
    } catch(NuxeoClientException $e) {
      $this->assertEquals(500, $e->getCode());

      /** @var NuxeoException $previous */
      $this->assertInstanceOf(NuxeoException::className, $previous = $e->getPrevious());
      $this->assertContains('MarshallingException', $previous->getType());
      $this->assertCount(119, $previous->getTrace());
      throw $e;
    }
    $this->fail('Should be Internal Server Error');
  }

}

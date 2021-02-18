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


use Nuxeo\Client\Constants;
use Nuxeo\Client\Objects;
use Nuxeo\Client\Request;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\NuxeoException;
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
    self::assertRequestPathMatches($client, $path, $requestIndex);
    self::assertInstanceOf(Objects\Document::class, $document);
    self::assertEquals($type, $document->getType());
    self::assertEquals($entityType, $document->getEntityType());
  }

  public function testConnectableRepository() {
    $client = $this->getClient();
    $repository = $client->repository();

    self::assertEquals($client->getBaseUrl(), $repository->getBaseUrl());
    self::assertEquals($client->getHttpClient(), $repository->getHttpClient());
    self::assertEquals($client->getInterceptors(), $repository->getInterceptors());
  }

  public function testFetchDocumentRoot() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('documentRoot.json'))
      ->addResponse($this->createJsonResponseFromFile('documentRoot.json'));

    $repositoryName = self::DOC_REPOSITORY;

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentRoot();

    $this->assertFetched($client, 'path/', $document, 'Root', 'document');
    self::assertEquals('/', $document->getPath());

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentRoot(self::DOC_REPOSITORY);

    $this->assertFetched($client, "repo/${repositoryName}/path/", $document, 'Root', 'document', 1);
    self::assertEquals($repositoryName, $document->getRepositoryName());
    self::assertEquals('/', $document->getPath());
  }

  public function testFetchDocumentByPath() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document.json'))
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    $repositoryName = self::DOC_REPOSITORY;

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentByPath(self::DOC_PATH);

    $this->assertFetched($client, 'path'.self::DOC_PATH, $document, self::DOC_TYPE, 'document');
    self::assertEquals(self::DOC_PATH, $document->getPath());

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentByPath(self::DOC_PATH, self::DOC_REPOSITORY);

    $this->assertFetched($client, "repo/${repositoryName}/path".self::DOC_PATH, $document, self::DOC_TYPE, 'document', 1);
    self::assertEquals($repositoryName, $document->getRepositoryName());
    self::assertEquals(self::DOC_PATH, $document->getPath());
  }

  public function testFetchDocument() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document.json'))
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    $uid = self::DOC_UID;
    $repositoryName = self::DOC_REPOSITORY;

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentById(self::DOC_UID);

    $this->assertFetched($client, 'id/'.self::DOC_UID, $document, 'Note', 'document');
    self::assertEquals(self::DOC_UID, $document->getUid());
    self::assertFalse($document->isTrashed());
    self::assertFalse($document->isProxy());
    self::assertFalse($document->isVersion());

    /** @var Objects\Document $document */
    $document = $client->repository()->fetchDocumentById($uid, 'default');

    $this->assertFetched($client, "repo/${repositoryName}/id/".$uid, $document, 'Note', 'document', 1);
    self::assertEquals($repositoryName, $document->getRepositoryName());
    self::assertEquals($uid, $document->getUid());
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

    self::assertEquals('POST', $client->getRequest()->getMethod());
    self::assertNotNull($decoded = json_decode((string) $client->getRequest()->getBody(), true));
    self::assertArraySubset(array(
      'entity-type' => 'document',
      'type' => self::DOC_TYPE,
      'title' => self::DOC_TITLE,
      'properties' => array(
        'dc:title' => self::DOC_TITLE
      )
    ), $decoded);

    $this->assertFetched($client, "path${parentPath}", $document, self::DOC_TYPE, 'document');
    self::assertEquals(self::DOC_TITLE, $document->getProperty('dc:title'));
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

    self::assertEquals('PUT', $client->getRequest()->getMethod());
    $this->assertFetched($client, "id/${uid}", $updated, $document->getType(), 'document');
    self::assertEquals($document->getProperty('dc:title'), $updated->getProperty('dc:title'));

    /** @var Objects\Document $updated */
    $updated = $client->repository()->updateDocumentByPath($path, $document);

    self::assertEquals('PUT', $client->getRequest()->getMethod());
    $this->assertFetched($client, "path${path}", $updated, $document->getType(), 'document', 1);
    self::assertEquals($document->getProperty('dc:title'), $updated->getProperty('dc:title'));
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
    self::assertRequestPathMatches($client, "id/${uid}");

    $client->repository()->deleteDocumentByPath($path);
    self::assertRequestPathMatches($client, "path${path}");

    /** @var Request $request */
    foreach($client->getRequests() as $request) {
      self::assertEquals('DELETE', $request->getMethod());
    }
  }

  public function testQuery() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document-list.json'));

    /** @var Objects\Documents $documents */
    $documents = $client->repository()->query('SELECT * FROM Document');

    self::assertRequestPathMatches($client, 'query');
    self::assertEquals('GET', $client->getRequest()->getMethod());

    self::assertEquals('documents', $documents->getEntityType());
    self::assertInstanceOf(Objects\Documents::class, $documents);
    self::assertCount(5, $documents);
    self::assertEquals(34, $documents->getTotalSize());

    /** @var Objects\Document $document */
    self::assertInstanceOf(Objects\Document::class, $document = $documents[0]);

    self::assertEquals(self::DOC_UID, $document->getUid());
    self::assertEquals(self::DOC_TYPE, $document->getType());
  }

  public function testMarshalling() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    /** @var MyDocType $document */
    $document = $client->repository()->fetchDocumentById(self::DOC_UID, null, MyDocType::class);
    self::assertInstanceOf(MyDocType::class, $document);
    self::assertNotNull($document->getCreatedAt());
  }

  public function testFail() {
    $this->expectException(NuxeoClientException::class);

    $client = $this->getClient()->addResponse($this->createResponse(404));
    $client->repository()->fetchDocumentById('404');
    self::fail('Should be not found');
  }

  public function testServerError() {
    $this->expectException(NuxeoClientException::class);

    $client = $this->getClient()
      ->addResponse($this->createResponse(
        500,
        array('Content-Type' => Constants::CONTENT_TYPE_JSON),
        file_get_contents($this->getResource('exception.json'))
      ));

    try {
      $client->repository()->fetchDocumentById('404');
    } catch(NuxeoClientException $e) {
      self::assertEquals(500, $e->getCode());

      /** @var NuxeoException $previous */
      self::assertInstanceOf(NuxeoException::class, $previous = $e->getPrevious());
      self::assertCount(119, $previous->getTrace());
      throw $e;
    }
    self::fail('Should be Internal Server Error');
  }

  public function testDocumentFetchBlob() {
    $content = file_get_contents($this->getResource(self::IMG_FS_PATH));

    $client = $this->getClient()
      ->addResponse($this->createResponse(
        200,
        array(
          'Content-Type' => self::IMG_MIME,
          'Content-Disposition' => 'attachment; filename*=UTF-8\'\''.self::IMG_FS_PATH
        ),
        $content
      ));

    $blob = Objects\Document::create($client)
      ->setUid(self::DOC_UID)
      ->fetchBlob();

    self::assertEquals(self::IMG_FS_PATH, $blob->getFilename());
    self::assertEquals(self::IMG_MIME, $blob->getMimeType());
    self::assertEquals(md5_file($this->getResource(self::IMG_FS_PATH)), md5($blob->getStream()->getContents()));
  }

  public function testDocumentFetchChildren() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponseFromFile('document-list.json'));

    $parent = Objects\Document::create($client)
      ->setUid(self::DOC_UID);
    $children = $parent->fetchChildren();
    self::assertEquals(5, $children->getCurrentPageSize());
  }

  public function testDocumentEnrichers() {
    $entityType = 'document';

    $client = $this->getClient()
      ->enrichers($entityType, 'invalid')
      ->enrichers($entityType, 'acls')
      ->addResponse($this->createJsonResponseFromFile('document.json'));

    /** @var Objects\Document $document */
    $document = $client->repository()
      ->enrichers($entityType, 'breadcrumb', true)
      ->fetchDocumentById(self::DOC_UID);

    self::assertCount(1, $requests = $client->getRequests());

    /** @var Request $request */
    [$request] = $requests;

    self::assertEquals('acls, breadcrumb', $request->getHeaderLine(Constants::HEADER_ENRICHERS.$entityType));
    self::assertCount(1, $document->getContextParameters('acls'));
  }

}

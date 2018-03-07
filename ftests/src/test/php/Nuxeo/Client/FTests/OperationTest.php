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

namespace Nuxeo\Client\FTests;


use Nuxeo\Client\Api\Objects\Blob\Blob;
use Nuxeo\Client\Api\Objects\Document;
use Nuxeo\Client\Api\Objects\Documents;
use Nuxeo\Client\Api\Objects\Operation\DirectoryEntries;
use Nuxeo\Client\FTests\Framework\TestCase;

class OperationTest extends TestCase {

  /**
   * @group server
   */
  public function testListDocuments() {
    /** @var Documents $documents */
    $documents = $this->getClient()
      ->schemas('*')
      ->automation('Repository.Query')
      ->param('query', 'SELECT * FROM Document')
      ->execute(Documents::className);

    $this->assertGreaterThan(5, $documents->size());
  }

  /**
   * @group server
   */
  public function testCreateDocument() {
    /** @var Document $doc */
    $doc = $this->getClient()
      ->automation('Document.Create')
      ->input('doc:/')
      ->params(array(
        'type' => 'File',
        'name' => 'Some file',
        'properties' => 'dc:title=Some file'
      ))->execute(Document::className);

    $this->assertNotNull($doc->getUid());

    $this->getClient()
      ->automation('Blob.Attach')
      ->param('document', $doc->getPath())
      ->input(Blob::fromFile($this->getResource('nuxeo.png'), null))
      ->execute(Blob::className);

    $blob = $this->getClient()
      ->voidOperation(false)
      ->automation('Blob.Get')
      ->input($doc->getPath())
      ->execute(Blob::className);

    $this->assertInstanceOf(Blob::className, $blob);
  }

  /**
   * @group server
   */
  public function testDirectories() {
    /** @var DirectoryEntries $continents */
    $continents = $this->getClient()
      ->automation('Directory.Entries')
      ->param('directoryName', 'continent')
      ->execute(DirectoryEntries::className);

    $this->assertCount(7, $continents);
  }

}

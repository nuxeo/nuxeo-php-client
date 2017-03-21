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
use Nuxeo\Client\FTests\Framework\TestCase;

class RepositoryTest extends TestCase {

  /**
   * @group server
   */
  public function testFetchDocument() {
    $repository = $this->getClient()->repository();

    /** @var Document $workspace */
    $workspace = $repository->fetchDocumentByPath('/default-domain/UserWorkspaces/Administrator');

    /** @var Document $doc */
    $doc = $repository->createDocumentById($workspace->getUid(), Document::create()
      ->setType('File')
      ->setName('Some file'));

    $this->getClient()
      ->automation('Blob.Attach')
      ->param('document', $doc->getPath())
      ->input(Blob::fromFile($this->getResource('nuxeo.png'), null))
      ->execute();

    $blob = $doc->fetchBlob();

    $this->assertNotNull($blob);

  }

}

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
use Nuxeo\Client\Api\Request;
use Nuxeo\Client\Tests\Framework\TestCase;

class RepositoryTest extends TestCase {

  public function testFetchDocumentRoot() {
    $client = $this->getClient()
      ->addResponse($this->createJsonResponse('{}'));

    $client
      ->repository()
      ->fetchDocumentRoot();

    $this->assertCount(1, $requests = $client->getRequests());

    /** @var \Nuxeo\Client\Internals\Api\Request $request */
    list($request) = $requests;
    $this->assertEquals(sprintf('/%spath', Constants::API_PATH), $request->getUrl(true)->getPath());
  }

}

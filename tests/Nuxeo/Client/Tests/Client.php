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

use Nuxeo\Client\Api\NuxeoClient as BaseClient;
use Nuxeo\Client\Api\Request;
use Nuxeo\Client\Api\Response;
use Nuxeo\Client\Tests\Http\Client as HttpClient;
use Nuxeo\Client\Tests\Util\ArrayIterator;

class Client extends BaseClient {

  protected $httpClient;

  protected function getHttpClient() {
    if(null === $this->httpClient) {
      $this->httpClient = new HttpClient();
    }
    return $this->httpClient;
  }

  /**
   * @param Response $response
   * @return Client $this
   */
  public function addResponse($response) {
    $this->getHttpClient()->addResponse($response);
    return $this;
  }

  /**
   * @return array
   */
  public function getRequests() {
    return $this->getHttpClient()->getRequests();
  }

  /**
   * @param int $index
   * @return Request
   */
  public function getRequest($index = 0) {
    return ArrayIterator::fromArray($this->getHttpClient()->getRequests())->offsetGet($index);
  }

}

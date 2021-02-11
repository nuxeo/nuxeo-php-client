<?php
/**
 * (C) Copyright 2018 Nuxeo SA (http://nuxeo.com/) and contributors.
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

namespace Nuxeo\Client\FTests\Framework;

use Nuxeo\Client\NuxeoClient;
use Nuxeo\Client\Tests\Framework\TestCase as BaseCase;

class TestCase extends BaseCase {

  const URL = 'http://localhost:8080/nuxeo/';

  /**
   * @param string $url
   * @param string $username
   * @param string $password
   * @return NuxeoClient
   * @throws \Nuxeo\Client\Spi\NuxeoClientException
   */
  protected function getClient($url = self::URL, $username = self::LOGIN, $password = self::PASSWORD) {
    if(null === $this->client) {
      $this->client = new NuxeoClient($url, $username, $password);
    }
    return $this->client;
  }

}

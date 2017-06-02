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

namespace Nuxeo\Client\Auth;

use Nuxeo\Client\Spi\Auth\AuthenticationInterceptor;
use Nuxeo\Client\Spi\Http\Client;
use Nuxeo\Client\Spi\NuxeoClientException;

class BasicAuthentication implements AuthenticationInterceptor {

  protected $username;

  protected $password;

  /**
   * BasicAuthentication constructor.
   * @param $username
   * @param $password
   */
  public function __construct($username, $password) {
    $this->username = $username;
    $this->password = $password;
  }

  /**
   * @param Client $client
   * @param \Nuxeo\Client\Request $request
   * @throws NuxeoClientException
   */
  public function proceed($client, $request) {
    try {
      $request->setAuth($this->username, $this->password);
    } catch(\InvalidArgumentException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }
  }

}

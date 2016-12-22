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

namespace Nuxeo\Client\Api\Auth;


use Guzzle\Common\Exception\GuzzleException;
use Guzzle\Http\Message\RequestInterface;
use Nuxeo\Client\Internals\Spi\Auth\AuthenticationInterceptor;

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
   * @param RequestInterface $request
   * @throws GuzzleException
   */
  public function proceed($request) {
    $request->setAuth($this->username, $this->password);
  }

}
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
 */

namespace Nuxeo\Client\Api\Auth;


use Guzzle\Http\Message\RequestInterface;
use Nuxeo\Client\Internals\Spi\Auth\AuthenticationInterceptor;

class TokenAuthentication implements AuthenticationInterceptor {

  const HEADER_TOKEN = 'X-Authentication-Token';

  protected $token;

  /**
   * TokenAuthentication constructor.
   * @param $token
   */
  public function __construct($token) {
    $this->token = $token;
  }

  /**
   * @param RequestInterface $request
   */
  public function proceed($request) {
    $request->addHeader(self::HEADER_TOKEN, $this->token);
  }

}

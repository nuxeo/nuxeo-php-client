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

namespace Nuxeo\Client\Internals\Spi;


use Guzzle\Http\Message\RequestInterface;

class SimpleInterceptor implements Interceptor {

  protected $callable;

  /**
   * SimpleInterceptor constructor.
   * @param callable $callable
   */
  public function __construct($callable) {
    $this->callable = $callable;
  }

  /**
   * @param RequestInterface $request
   */
  public function proceed($request) {
    $callable = $this->callable;
    $callable($request);
  }


}

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

namespace Nuxeo\Client\Internals\Spi\Http;

use Nuxeo\Client\Api\Constants;
use Guzzle\Http\Client as BaseClient;
use Nuxeo\Client\Internals\Spi\Http\Message\RequestFactory;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;


class Client extends BaseClient {

  /**
   * Client constructor.
   * @param string $baseUrl
   * @param array $config
   * @throws NuxeoClientException
   */
  public function __construct($baseUrl = '', $config = null) {
    try {
      parent::__construct($baseUrl, $config);

      $this->setRequestFactory(new RequestFactory());
      $this->setDefaultOption('headers/content-type', Constants::CONTENT_TYPE_JSON_NXENTITY);
    } catch(\RuntimeException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }
  }
}

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

namespace Nuxeo\Automation\Client;

use Nuxeo\Automation\Client\Utilities\NuxeoRequest;
use Nuxeo\Client\Api\NuxeoClient;
use Nuxeo\Client\Internals\Compat\Request;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;


/**
 * Session class
 *
 * Class which stocks username,password, and open requests
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 * @deprecated Use \Nuxeo\Client\Api\NuxeoClient
 */
class NuxeoSession {

  /**
   * @var NuxeoClient
   */
  private $client;

  /**
   * @param $url
   * @param BasicAuth $auth
   * @param array $headers
   * @throws NuxeoClientException
   */
  public function __construct($url, BasicAuth $auth, $headers = array('Content-Type' => 'application/json+nxrequest')) {
    //TODO: use headers
    $this->client = new NuxeoClient($url, $auth->getUsername(), $auth->getPassword());
  }

  /**
   * @see http://explorer.nuxeo.com/nuxeo/site/distribution/current/listOperations List of available operations
   * @param string $operation
   * @return NuxeoRequest
   */

  public function newRequest($operation) {
    return new Request($this->client, $operation);
  }
}

<?php
/**
 * (C) Copyright 2015 Nuxeo SA (http://nuxeo.com/) and contributors.
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the GNU Lesser General Public License
 * (LGPL) version 2.1 which accompanies this distribution, and is available at
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
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

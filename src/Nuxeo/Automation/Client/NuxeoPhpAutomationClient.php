<?php
/**
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
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


use Nuxeo\Client\Internals\Spi\NuxeoClientException;

/**
 * Class NuxeoPhpAutomationClient
 * @package Nuxeo\Automation\Client
 * @deprecated Use \Nuxeo\Client\Api\NuxeoClient
 */
class NuxeoPhpAutomationClient {

  protected $url;

  public function __construct($url = 'http://localhost:8080/nuxeo/site/automation') {
    $this->url = $url;
  }

  /**
   * @param string $username
   * @param string $password
   * @return NuxeoSession
   * @throws NuxeoClientException
   */
  public function getSession($username = 'Administrator', $password = 'Administrator') {
    return new NuxeoSession($this->url, new BasicAuth($username, $password));
  }
}

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

namespace Nuxeo\Client\Tests\Http;


use Nuxeo\Client\Api\Response;
use Nuxeo\Client\Internals\Spi\Http\Client as BaseClient;

class Client extends BaseClient {

  private $requests;

  private $responses;

  public function __construct($baseUrl = '', $config = null) {
    parent::__construct($baseUrl, $config);

    $this->requests = array();
    $this->responses = array();
  }

  /**
   * @param Response $response
   */
  public function addResponse($response) {
    $this->responses[] = $response;
  }

  /**
   * @return array
   */
  public function getRequests() {
    return $this->requests;
  }

  /**
   * @param array|\Nuxeo\Client\Api\Request $requests
   * @return array|Response|null
   */
  public function send($requests) {
    list($response) = $this->sendMultiple(array($requests));

    return $response;
  }

  protected function sendMultiple(array $requests) {
    $responses = array();

    /** @var \Nuxeo\Client\Api\Request $request */
    foreach($requests as $request) {
      // Get body to simulate sending the request (required for multipart/related)
      $request->getBody();

      $this->requests[] = $request;

      if(empty($this->responses)) {
        $responses[] = new Response(500);
      } else {
        $responses[] = $response = array_shift($this->responses);
        $request->startResponse($response);
      }
    }
    return $responses;
  }

}

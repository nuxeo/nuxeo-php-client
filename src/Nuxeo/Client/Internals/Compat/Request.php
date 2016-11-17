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

namespace Nuxeo\Client\Internals\Compat;


use Nuxeo\Automation\Client\Internals\NuxeoClientException;
use Nuxeo\Automation\Client\NuxeoDocuments;
use Nuxeo\Automation\Client\Utilities\NuxeoRequest;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\NuxeoClient;
use Nuxeo\Client\Api\Objects\Blob;
use Nuxeo\Client\Api\Objects\Blobs;

class Request extends NuxeoRequest {

  /**
   * @var NuxeoClient
   */
  protected $client;

  /**
   * @var Operation
   */
  protected $operation;

  /**
   * @var Blobs
   */
  protected $blobs;

  /**
   * Request constructor.
   * @param NuxeoClient $client
   * @param string $operation
   */
  public function __construct(NuxeoClient $client, $operation) {
    $this->blobs = new Blobs();
    $this->client = $client;

    $url = clone $client->getBaseUrl();
    $this->operation = new Operation($client, $url->addPath(Constants::AUTOMATION_PATH), $operation);
  }

  /**
   * @return Blobs
   */
  public function getBlobs() {
    return $this->blobs;
  }

  public function setSchema($schema = '*') {
    $this->client->schemas($schema);
    return $this;
  }

  public function setX_NXVoidOperation($headerValue = '*') {
    $this->client->voidOperation(true);
    return $this;
  }

  public function set($requestType, $requestContentOrVarName, $requestVarVallue = NULL) {
    if('params' === $requestType) {
      $this->operation->param($requestContentOrVarName, $requestVarVallue);
    } elseif('input' === $requestType) {
      $this->operation->input($requestContentOrVarName);
    }
    return $this;
  }

  public function loadBlob($path, $contentType = 'application/binary') {
    try {
      $this->getBlobs()->add(Blob::fromFile($path, $contentType));
    } catch(\Nuxeo\Client\Internals\Spi\NuxeoClientException $ex) {
      throw new NuxeoClientException($ex->getMessage());
    }

    return parent::loadBlob($path, $contentType);
  }

  public function sendRequest() {
    if($this->getBlobs()) {
      $this->operation->input($this->getBlobs());
    }

    $response = $this->operation->doExecute();
    $json = json_decode($response->getBody(true), true);
    if(null === $json) {
      /** @var Blob $blob */
      $blob = $this->operation->computeResponse($response, Blob::className);
      return file_get_contents($blob->getFile()->getPathname());
    } else {
      return new NuxeoDocuments($json);
    }
  }

}
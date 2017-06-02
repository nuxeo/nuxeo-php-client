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

namespace Nuxeo\Client\Api\Objects;


use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\NuxeoClient;
use Nuxeo\Client\Api\Objects\Blob\Blob;
use Nuxeo\Client\Api\Objects\Blob\Blobs;
use Nuxeo\Client\Internals\Spi\ClassCastException;
use Nuxeo\Client\Internals\Spi\Http\Method\POST;
use Nuxeo\Client\Internals\Spi\NoSuchOperationException;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;
use Nuxeo\Client\Internals\Spi\Objects\NuxeoEntity;
use Nuxeo\Client\Internals\Spi\Objects\Operation\OperationBody;

class Operation extends NuxeoEntity {

  /**
   * @var string
   */
  protected $operationId;

  /**
   * @var \Nuxeo\Client\Internals\Spi\Objects\Operation\OperationBody
   */
  protected $body;

  /**
   * Operation constructor.
   * @param NuxeoClient $nuxeoClient
   * @param string $operationId
   */
  public function __construct($nuxeoClient, $operationId = null) {
    parent::__construct(Constants::ENTITY_TYPE_OPERATION, $nuxeoClient);

    $this->operationId = $operationId;
    $this->body = new OperationBody();
  }

  /**
   * Adds an operation param.
   * @param string $name
   * @param string $value
   * @return Operation
   */
  public function param($name, $value) {
    $this->body->addParameter($name, $value);
    return $this;
  }

  /**
   * Adds operation params
   * @param array $params
   * @return Operation
   */
  public function params($params) {
    $this->body->addParameters($params);
    return $this;
  }

  /**
   * Sets operation params
   * @param $params
   * @return Operation
   */
  public function parameters($params) {
    $this->body->setParameters($params);
    return $this;
  }

  /**
   * @param mixed $input
   * @return Operation
   */
  public function input($input) {
    $this->body->setInput($input);
    return $this;
  }

  /**
   * @param string $type
   * @param string $operationId
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function execute($type = null, $operationId = null) {
    if(null === $operationId) {
      return $this->execute($type, $this->operationId);
    }

    $input = $this->body->getInput();
    $client = $this->getNuxeoClient();
    $blobs = null;

    if(null === $operationId) {
      throw new NoSuchOperationException($operationId);
    }

    if($input instanceof Blob) {
      $input = new Blobs(array($input));
    }

    if($input instanceof Blobs) {
      $blobs = array();
      foreach($input->getBlobs() as $blob) {
        $blobs[] = $blob->getFile()->getPathname();
      }
      $client->voidOperation(true);
    }

    return $this->getResponseNew(POST::create('automation/{operationId}')
      ->setBody($client->getConverter()->writeJSON($this->body))
      ->setFiles($blobs),
      $type);
  }

}

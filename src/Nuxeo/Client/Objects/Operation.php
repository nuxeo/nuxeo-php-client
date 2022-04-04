<?php
/**
 * (C) Copyright 2018 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 */

namespace Nuxeo\Client\Objects;


use Doctrine\Common\Annotations\AnnotationException;
use Nuxeo\Client\Constants;
use Nuxeo\Client\NuxeoClient;
use Nuxeo\Client\Objects\Blob\Blob;
use Nuxeo\Client\Objects\Blob\Blobs;
use Nuxeo\Client\Spi\Http\Message\RelatedFile;
use Nuxeo\Client\Spi\Http\Method\POST;
use Nuxeo\Client\Spi\NoSuchOperationException;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;
use Nuxeo\Client\Spi\Objects\Operation\OperationBody;

class Operation extends NuxeoEntity {

  /**
   * @var string
   */
  protected $operationId;

  /**
   * @var \Nuxeo\Client\Spi\Objects\Operation\OperationBody
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
   * @throws \RuntimeException
   * @throws \Nuxeo\Client\Spi\NoSuchOperationException
   * @throws \Nuxeo\Client\Spi\NuxeoClientException
   * @throws \Nuxeo\Client\Spi\ClassCastException
   */
  public function execute($type = null, $operationId = null) {
    if(null === $operationId) {
      return $this->execute($type, $this->operationId);
    }

    $input = $this->body->getInput();
    $files = [];

    if(null === $operationId) {
      throw new NoSuchOperationException($operationId);
    }

    if($input instanceof Blob) {
      $input = new Blobs(array($input));
    }

    if($input instanceof Blobs) {
      $this->voidOperation(true);
      foreach($input->getBlobs() as $blob) {
        $files[] = new RelatedFile($blob->getFilename(), $blob->getStream(), $blob->getMimeType());
      }
    }

    try {
      return $this->getResponseNew(POST::create('automation/{operationId}')
        ->setBody($this->getConverter()->writeJSON($this->body))
        ->setFiles($files),
        $type);
    } catch(AnnotationException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }
  }

}

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

/**
 *
 * @author Pierre-Gildas MILLON <pgmillon@gmail.com>
 */

namespace Nuxeo\Client\Api\Objects;


use Guzzle\Http\Url;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\NuxeoClient;

class Operation extends NuxeoEntity {

  /**
   * @var string
   */
  protected $operationId;

  /**
   * @var NuxeoClient
   */
  private $nuxeoClient;

  /**
   * @var Url
   */
  private $apiUrl;

  /**
   * @var array
   */
  private $params = array();

  private $input;

  /**
   * Operation constructor.
   * @param string $operationId
   * @param NuxeoClient $nuxeoClient
   * @param Url $apiUrl
   */
  public function __construct($operationId, $nuxeoClient, $apiUrl) {
    parent::__construct(Constants::ENTITY_TYPE_OPERATION);

    $this->operationId = $operationId;
    $this->nuxeoClient = $nuxeoClient;
    $this->apiUrl = $apiUrl;
  }

  /**
   * Adds an operation param.
   * @param string $name
   * @param string $value
   * @return Operation
   */
  public function param($name, $value) {
    $this->params[$name] = $value;
    return $this;
  }

  /**
   * Adds operation params
   * @param array $params
   * @return Operation
   */
  public function params($params) {
    $this->params = array_merge($this->params, $params);
    return $this;
  }

  /**
   * Sets operation params
   * @param $params
   * @return Operation
   */
  public function parameters($params) {
    $this->params = $params;
    return $this;
  }

  /**
   * @param mixed $input
   * @return Operation
   */
  public function input($input) {
    $this->input = $input;
    return $this;
  }

  /**
   * @param $clazz
   * @return mixed
   */
  public function execute($clazz) {
    $body = str_replace('\/', '/', json_encode(array(
      'params' => $this->getParams()
    ), JSON_FORCE_OBJECT));

    $response = $this->getNuxeoClient()->post($this->computeRequestUrl(), $body);

    return $this->computeResponse($response, $clazz);
  }

  /**
   * @return array
   */
  protected function getParams() {
    return $this->params;
  }

  /**
   * @return NuxeoClient
   */
  protected function getNuxeoClient() {
    return $this->nuxeoClient;
  }

  /**
   * @return Url
   */
  protected function computeRequestUrl() {
    return $this->apiUrl->addPath($this->operationId);
  }

}
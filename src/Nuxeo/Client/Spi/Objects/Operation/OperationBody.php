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

namespace Nuxeo\Client\Spi\Objects\Operation;

use JMS\Serializer\Annotation as Serializer;

class OperationBody {

  /**
   * @Serializer\SerializedName("params")
   * @Serializer\Accessor(getter="serializeParams",setter="setParameters")
   * @var array
   */
  protected $parameters = array();

  /**
   * @var array
   */
  protected $context;

  /**
   * @var mixed
   */
  protected $input;

  /**
   * @param string $name
   * @param mixed $value
   * @return OperationBody
   */
  public function addParameter($name, $value) {
    $this->parameters[$name] = $value;
    return $this;
  }

  /**
   * @param array $params
   * @return OperationBody
   */
  public function addParameters($params) {
    $this->parameters = array_merge($this->parameters, $params);
    return $this;
  }

  /**
   * @return array
   */
  public function getParameters() {
    return $this->parameters;
  }

  /**
   * @param array $parameters
   * @return OperationBody
   */
  public function setParameters($parameters) {
    $this->parameters = $parameters;
    return $this;
  }

  /**
   * @return array
   */
  public function getContext() {
    return $this->context;
  }

  /**
   * @param array $context
   * @return OperationBody
   */
  public function setContext($context) {
    $this->context = $context;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getInput() {
    return $this->input;
  }

  /**
   * @param mixed $input
   * @return OperationBody
   */
  public function setInput($input) {
    $this->input = $input;
    return $this;
  }

  /**
   * Enforce empty as {} instead of []
   * @return array|\stdClass
   */
  public function serializeParams() {
    if(empty($this->parameters)) {
      return new \stdClass();
    } else {
      return $this->parameters;
    }
  }

}

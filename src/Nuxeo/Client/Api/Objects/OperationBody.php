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

namespace Nuxeo\Client\Api\Objects;

use JMS\Serializer\Annotation as Serializer;

class OperationBody {

  /**
   * @Serializer\SerializedName("params")
   * @var array
   */
  protected $parameters;

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

}
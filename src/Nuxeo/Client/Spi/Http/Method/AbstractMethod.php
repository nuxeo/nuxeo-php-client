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

namespace Nuxeo\Client\Spi\Http\Method;


abstract class AbstractMethod {

  /**
   * Inspired from https://github.com/nikic/FastRoute
   * {varName}
   */
  const VARIABLE_REGEX = <<<'REGEX'
\{
    \s* ([a-zA-Z_][a-zA-Z0-9_-]*) \s*
\}
REGEX;

  /**
   * @var string
   */
  private $path;

  /**
   * @var string
   */
  private $name;

  /**
   * @var string
   */
  private $body;

  /**
   * @var array
   */
  private $files = array();

  /**
   * HttpMethod constructor.
   * @param string $name
   * @param string $path
   */
  public function __construct($name, $path) {
    $this->name = $name;
    $this->path = $path;
  }

  /**
   * Inspired from https://github.com/nikic/FastRoute
   * @param array $params
   * @return string
   * @throws \InvalidArgumentException
   */
  public function computePath($params) {
    $route = $this->getPath();

    if (!preg_match_all(
      '~' . self::VARIABLE_REGEX . '~x', $route, $matches,
      PREG_OFFSET_CAPTURE | PREG_SET_ORDER
    )) {
      return $route;
    }

    $offset = 0;
    $url = '';

    foreach ($matches as $set) {
      if ($set[0][1] > $offset) {
        $url .= substr($route, $offset, $set[0][1] - $offset);
      }
      if(isset($params[$set[1][0]])) {
        $url .= $params[$set[1][0]];
      } else {
        throw new \InvalidArgumentException(sprintf('No value supplied for "%s"', $set[1][0]));
      }
      $offset = $set[0][1] + strlen($set[0][0]);
    }
    if ($offset !== strlen($route)) {
      $url .= substr($route, $offset);
    }

    return $url;
  }

  /**
   * @return string
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getBody() {
    return $this->body;
  }

  /**
   * @param mixed $body
   * @return AbstractMethod
   */
  public function setBody($body) {
    $this->body = $body;
    return $this;
  }

  /**
   * @return array
   */
  public function getFiles() {
    return $this->files;
  }

  /**
   * @param mixed $files
   * @return AbstractMethod
   */
  public function setFiles($files) {
    $this->files = $files;
    return $this;
  }

}

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

namespace Nuxeo\Client\Objects\Operation;


use JMS\Serializer\Annotation as Serializer;

class Action {

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $id;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $link;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $icon;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $label;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $help;

  /**
   * @return string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getLink() {
    return $this->link;
  }

  /**
   * @return string
   */
  public function getIcon() {
    return $this->icon;
  }

  /**
   * @return string
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * @return string
   */
  public function getHelp() {
    return $this->help;
  }

}

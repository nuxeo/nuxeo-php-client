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

namespace Nuxeo\Client\Api\Objects;


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Api\Constants;


class Repository extends NuxeoEntity {

  /**
   * Repository constructor.
   * @param $nuxeoClient
   * @param \Guzzle\Http\Url $apiUrl
   */
  public function __construct($nuxeoClient, $apiUrl) {
    parent::__construct(Constants::ENTITY_TYPE_DOCUMENT, $nuxeoClient);
  }

  /**
   * @param string $type
   * @return mixed
   */
  public function fetchDocumentRoot($type = null) {
    return $this->getResponse($type);
  }

}

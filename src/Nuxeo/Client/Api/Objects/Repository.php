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
use Nuxeo\Client\Internals\Spi\Annotations\GET;
use Nuxeo\Client\Internals\Spi\Objects\NuxeoEntity;


class Repository extends NuxeoEntity {

  /**
   * Repository constructor.
   * @param $nuxeoClient
   */
  public function __construct($nuxeoClient) {
    parent::__construct(Constants::ENTITY_TYPE_DOCUMENT, $nuxeoClient);
  }

  /**
   * @GET("path")
   * @param string $type
   * @return mixed
   * @throws \Nuxeo\Client\Internals\Spi\NuxeoClientException
   * @throws \Nuxeo\Client\Internals\Spi\ClassCastException
   */
  public function fetchDocumentRoot($type = null) {
    return $this->getResponse($type);
  }

}

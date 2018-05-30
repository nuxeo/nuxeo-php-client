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
use Nuxeo\Client\NuxeoClient;
use Nuxeo\Client\Objects\Document;
use Nuxeo\Client\Spi\ClassCastException;
use Nuxeo\Client\Spi\NoSuchOperationException;
use Nuxeo\Client\Spi\NuxeoClientException;

class DocRef {

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $ref;

  /**
   * @var NuxeoClient
   */
  protected $nuxeoClient;

  /**
   * DocRef constructor.
   * @param string $ref
   * @param NuxeoClient $nuxeoClient
   */
  public function __construct($ref, $nuxeoClient = null) {
    $this->ref = $ref;
    $this->nuxeoClient = $nuxeoClient;
  }

  /**
   * @return string
   */
  public function getRef() {
    return $this->ref;
  }

  /**
   * @param string $type
   * @throws ClassCastException
   * @throws NuxeoClientException
   * @return Document
   */
  public function getDocument($type = Document::class) {
    if(null !== $this->nuxeoClient) {
      try {
        return $this->nuxeoClient
          ->automation('Document.Fetch')
          ->param('value', $this->getRef())
          ->execute($type);
      } catch(NoSuchOperationException|\RuntimeException $e) {
        throw NuxeoClientException::fromPrevious($e, 'Could not fetch linked document');
      }
    }
    return null;
  }

}

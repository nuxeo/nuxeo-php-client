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

namespace Drupal\Nuxeo\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Psr7\Response;
use Nuxeo\Client\Constants;
use Nuxeo\Client\Objects\Document;
use Nuxeo\Client\Objects\Documents;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Tests\Client as NuxeoClient;

class NuxeoController extends ControllerBase {

  /**
   * @return string[]
   * @throws \RuntimeException
   * @throws \LogicException
   * @throws \Nuxeo\Client\Spi\NuxeoClientException
   */
  protected function fetchDocuments() {
    $client = new NuxeoClient;

    $file = new \SplFileObject(__DIR__ . '/../../resources/document-list.json', 'rb', true);
    $client->addResponse(new Response(200, ['Content-Type' => Constants::CONTENT_TYPE_JSON], file_get_contents($file->getRealPath())));

    $documents = (function ($items) {
      /** @var Documents $items */
      foreach($items as $key => $item) {
        /** @var Document $item */
        yield $key => $item->getTitle();
      }
    })($client
      ->repository()
      ->query('SELECT * FROM Document WHERE dc:title IS NOT NULL AND ecm:path STARTSWITH "/default-domain"'));
    return $documents;
  }

  public function content() {
    try {
      $documentList = sprintf('<ul>%s</ul>', implode(iterator_to_array((function ($items) {
        /** @var string[] $items */
        foreach($items as $item) {
          yield "<li>$item</li>";
        }
      })($this->fetchDocuments()))));
    } catch(NuxeoClientException $e) {
      $documentList = sprintf('<p>Failed to fetch documents from Nuxeo:</p><pre>%s</pre>', $e->__toString());
    }
    return [
      '#type' => 'markup',
      '#markup' => $this->t($documentList)
    ];
  }

}

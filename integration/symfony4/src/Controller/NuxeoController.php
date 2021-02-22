<?php
/*
 * (C) Copyright 2021 Nuxeo SA (http://nuxeo.com/) and contributors.
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

namespace App\Controller;


use Nuxeo\Client\Constants;
use Nuxeo\Client\Objects\Documents;
use Nuxeo\Client\Tests\Client as NuxeoClient;
use Symfony\Component\HttpFoundation\Response;

class NuxeoController {

  /**
   * @return Documents
   */
  protected function fetchDocuments() {
    $client = new NuxeoClient;

    $file = new \SplFileObject('document-list.json', 'rb', true);
    $client->addResponse(new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => Constants::CONTENT_TYPE_JSON], file_get_contents($file->getRealPath())));

    return $client->repository()
      ->query('SELECT * FROM Document WHERE dc:title IS NOT NULL AND ecm:path STARTSWITH "/default-domain"');
  }

  public function index() {
    $documents = $this->fetchDocuments();

    ob_start();
      require __DIR__.'/../../templates/documentList.php';

    return new Response(ob_get_clean());
  }

}

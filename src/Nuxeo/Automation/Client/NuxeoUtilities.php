<?php
/**
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 * Contributors:
 *     Pierre-Gildas MILLON <pgmillon@nuxeo.com>
 */

namespace Nuxeo\Automation\Client;

use Nuxeo\Automation\Client\Internals\NuxeoClientException;


/**
 * Contains Utilities such as date wrappers
 */
class NuxeoUtilities {

  public function dateConverterPhpToNuxeo($date) {
    return date_format($date, 'Y-m-d');
  }

  public function dateConverterNuxeoToPhp($date) {
    $newDate = explode('T', $date);
    return new \DateTime($newDate[0]);
  }

  /**
   * @param string $date
   * @return \DateTime
   * @deprecated 1.1.0 please use \DateTime::createFromFormat
   */
  public function dateConverterInputToPhp($date) {
    return \DateTime::createFromFormat('Y/m/d', $date);
  }

  /**
   * Function Used to get Data from Nuxeo, such as a blob. MUST BE PERSONALISED. (Or just move the
   * headers)
   *
   * @param string $path path of the file
   * @throws NuxeoClientException
   * @deprecated Use \Nuxeo\Client\Api\Objects\Blob::fromFilename
   */
  function getFileContent($path) {

    $eurl = explode('/', $path);

    $client = new NuxeoPhpAutomationClient('http://localhost:8080/nuxeo/site/automation');

    try {
      $session = $client->getSession('Administrator', 'Administrator');
    } catch(\Nuxeo\Client\Internals\Spi\NuxeoClientException $ex) {
      throw new NuxeoClientException($ex->getMessage());
    }

    $answer = $session->newRequest('Chain.getDocContent')->set('context', 'path' . $path)
      ->sendRequest();

    if(null === $answer or false === $answer) {
      throw new NuxeoClientException('$answer is not set');
    } else {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename=' . end($eurl) . '.pdf');
      readfile('tempstream');
    }
  }
}

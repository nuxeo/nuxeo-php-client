<?php
/*
 * (C) Copyright 2015 Nuxeo SA (http://nuxeo.com/) and contributors.
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

namespace Nuxeo\Automation\Client;

use Nuxeo\Automation\Client\Internals\NuxeoClientException;


/**
 * Contains Utilities such as date wrappers
 */
class NuxeoUtilities {
  private $ini;

  public function dateConverterPhpToNuxeo($date) {
    return date_format($date, 'Y-m-d');
  }

  public function dateConverterNuxeoToPhp($date) {
    $newDate = explode('T', $date);
    $phpDate = new \DateTime($newDate[0]);
    return $phpDate;
  }

  /**
   * @param $date
   * @return \DateTime
   * @deprecated since 1.1.0 please use \DateTime::createFromFormat
   */
  public function dateConverterInputToPhp($date) {
    return \DateTime::createFromFormat("Y/m/d", $date);
  }

  /**
   * Function Used to get Data from Nuxeo, such as a blob. MUST BE PERSONALISED. (Or just move the
   * headers)
   *
   * @param $path path of the file
   */
  function getFileContent($path) {

    $eurl = explode("/", $path);

    $client = new NuxeoPhpAutomationClient('http://localhost:8080/nuxeo/site/automation');

    $session = $client->getSession('Administrator', 'Administrator');

    $answer = $session->newRequest("Chain.getDocContent")->set('context', 'path' . $path)
      ->sendRequest();

    if (!isset($answer) OR $answer == false)
      throw new NuxeoClientException('$answer is not set');
    else {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename=' . end($eurl) . '.pdf');
      readfile('tempstream');
    }
  }
}

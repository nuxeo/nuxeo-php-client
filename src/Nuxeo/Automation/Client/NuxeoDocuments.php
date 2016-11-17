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
 * Documents class
 *
 * hold an Array of Document
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 * @deprecated Use \Nuxeo\Client\Api\Objects\Documents
 */
class NuxeoDocuments {

  private $documentsList = array();

  public function __construct($newDocList) {
    if(!empty($newDocList['entries'])) {
      foreach($newDocList['entries'] as $entry) {
        $this->documentsList[] = new NuxeoDocument($entry);
      }
    } elseif(!empty($newDocList['uid'])) {
      $this->documentsList = array(new NuxeoDocument($newDocList));
    } elseif(is_array($newDocList)) {
      throw new NuxeoClientException('file not found');
    }
  }

  public function output() {
    $value = count($this->documentsList);
    echo '<table>';
    echo '<tr><TH>Entity-type</TH><TH>Repository</TH><TH>uid</TH><TH>Path</TH>
			<TH>Type</TH><TH>State</TH><TH>Title</TH><TH>Download as PDF</TH>';
    for ($test = 0; $test < $value; $test++) {
      echo '<tr>';
      current($this->documentsList)->output();
      echo '<td><form id="test" action="../tests/B5bis.php" method="post" >';
      echo '<input type="hidden" name="a_recup" value="' .
        current($this->documentsList)->getPath() . '"/>';
      echo '<input type="submit" value="download"/>';
      echo '</form></td></tr>';
      next($this->documentsList);
    }
    echo '</table>';
  }

  /**
   * @param $number
   * @return NuxeoDocument|null
   */
  public function getDocument($number) {
    return isset($this->documentsList[$number]) ? $this->documentsList[$number] : null;
  }

  public function getDocumentList() {
    return $this->documentsList;
  }
}

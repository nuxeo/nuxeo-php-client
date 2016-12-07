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

/**
 * Documents class
 *
 * hold an Array of Document
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class NuxeoDocuments {

  private $documentsList = array();

  public function __construct($newDocList) {
    if(!empty($newDocList['entries']) && is_array($newDocList['entries'])) {
      foreach($newDocList['entries'] as $entry) {
        $this->documentsList[] = new NuxeoDocument($entry);
      }
    } elseif(!empty($newDocList['uid'])) {
      $this->documentsList[] = new NuxeoDocument($newDocList);
    } elseif(is_array($newDocList)) {
      throw new \InvalidArgumentException('Could not create documents from given array');
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
    if($number > count($this->documentsList)) {
      return null;
    } else {
      return $this->documentsList[$number];
    }
  }

  public function getDocumentList() {
    return $this->documentsList;
  }
}

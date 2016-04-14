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
 * Documents class
 *
 * hold an Array of Document
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class NuxeoDocuments {

  private $documentsList;

  public function __construct($newDocList) {
    $this->documentsList = null;
    $test = true;
    if (!empty($newDocList['entries'])) {
      while (false !== $test) {
        if (is_array(current($newDocList['entries']))) {
          $this->documentsList[] = new NuxeoDocument(current($newDocList['entries']));
        }
        $test = each($newDocList['entries']);
      }
      $test = sizeof($this->documentsList);
      unset($this->documentsList[$test]);
    } elseif (!empty($newDocList['uid'])) {
      $this->documentsList[] = new NuxeoDocument($newDocList);
    } elseif (is_array($newDocList)) {
      throw new NuxeoClientException('file not found');
    } else {
      return $newDocList;
    }
  }

  public function output() {
    $value = sizeof($this->documentsList);
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
    $value = sizeof($this->documentsList);
    if ($number < $value AND $number >= 0)
      return $this->documentsList[$number];
    else
      return null;
  }

  public function getDocumentList() {
    return $this->documentsList;
  }
}

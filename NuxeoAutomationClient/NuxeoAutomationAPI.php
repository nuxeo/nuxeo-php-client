<?php
/*
 * (C) Copyright 2011 Nuxeo SA (http://nuxeo.com/) and contributors.
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the GNU Lesser General Public License
 * (LGPL) version 2.1 which accompanies this distribution, and is available at
 * http://www.gnu.org/licenses/lgpl.html
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * Contributors:
 *     Gallouin Arthur
 */

require_once('NuxeoAutomationUtilities.php');

/**
 * phpAutomationClient class
 *
 * Class which initializes the php client with an URL
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class NuxeoPhpAutomationClient {
    private $url;
    private $session;

    public function NuxeoPhpAutomationClient($url = 'http://localhost:8080/nuxeo/site/automation') {
        $this->url = $url;
    }

    /**
     * getSession function
     *
     * Open a session from a phpAutomationClient
     *
     * @var        $username : username for your session
     *                $password : password matching the usename
     * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
     */
    public function getSession($username = 'Administrator', $password = 'Administrator') {
        $this->session = $username . ":" . $password;
        $session = new NuxeoSession($this->url, $this->session);
        return $session;
    }
}

/**
 * Session class
 *
 * Class which stocks username,password, and open requests
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class NuxeoSession {

    private $urlLoggedIn;
    private $headers;
    private $requestContent;

    public function NuxeoSession($url, $session, $headers = "Content-Type: application/json+nxrequest") {
        $this->urlLoggedIn = str_replace("http://", "", $url);
        if (strpos($url, 'https') !== false) {
            $this->urlLoggedIn = "https://" . $session . "@" . $this->urlLoggedIn;
        } elseif (strpos($url, 'http') !== false) {
            $this->urlLoggedIn = "http://" . $session . "@" . $this->urlLoggedIn;
        } else {
            throw Exception;
        }
        $this->headers = $headers;
    }

    /**
     * newRequest function
     *
     * Create a request from a session
     *
     * @var        $requestType : type of request you want to execute (such as Document.Create
     *               for exemple)
     * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
     */
    public function newRequest($requestType) {
        $newRequest = new NuxeoRequest($this->urlLoggedIn, $this->headers, $requestType);
        return $newRequest;
    }
}

/**
 * Document class
 *
 * hold a return document
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class NuxeoDocument {

    Private $object;
    Private $properties;

    Public function NuxeoDocument($newDocument) {
        $this->object = $newDocument;
        if (array_key_exists('properties', $this->object))
            $this->properties = $this->object['properties'];
        else
            $this->properties = null;
    }

    public function getUid() {
        return $this->object['uid'];
    }

    public function getPath() {
        return $this->object['path'];
    }

    public function getType() {
        return $this->object['type'];
    }

    public function getState() {
        return $this->object['state'];
    }

    public function getTitle() {
        return $this->object['title'];
    }

    Public function output() {
        $value = sizeof($this->object);

        for ($test = 0; $test < $value - 1; $test++) {
            echo '<td> ' . current($this->object) . '</td>';
            next($this->object);
        }

        if ($this->properties !== NULL) {
            $value = sizeof($this->properties);
            for ($test = 0; $test < $value; $test++) {
                echo '<td>' . key($this->properties) . ' : ' .
                     current($this->properties) . '</td>';
                next($this->properties);
            }
        }
    }

    public function getObject() {
        return $this->object;
    }

    public function getProperty($schemaNamePropertyName) {
        if (array_key_exists($schemaNamePropertyName, $this->properties)) {
            return $this->properties[$schemaNamePropertyName];
        }
        else
            return null;
    }
}

/**
 * Documents class
 *
 * hold an Array of Document
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class NuxeoDocuments {

    private $documentsList;

    public function NuxeoDocuments($newDocList) {
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
            echo 'file not found';
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
        $phpDate = new DateTime($newDate[0]);
        return $phpDate;
    }

    public function dateConverterInputToPhp($date) {

        $edate = explode('/', $date);
        $day = $edate[2];
        $month = $edate[1];
        $year = $edate[0];

        if ($month > 0 AND $month < 12)
            if ($month % 2 == 0)
                if ($day < 1 OR $day > 31) {
                    echo 'date not correct';
                    exit;
                }
                elseif ($month == 2)
                    if (year % 4 == 0)
                        if ($day > 29 OR $day < 0) {
                            echo 'date not correct';
                            exit;
                        }
                        else
                            if ($day > 28 OR $day < 0) {
                                echo 'date not correct';
                                exit;
                            }
                            else
                                if ($day > 30 OR $day < 0) {
                                    echo 'date not correct';
                                    exit;
                                }

        $phpDate = new DateTime($year . '-' . $month . '-' . $day);

        return $phpDate;
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
            echo '$answer is not set';
        else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . end($eurl) . '.pdf');
            readfile('tempstream');
        }
    }
}

?>

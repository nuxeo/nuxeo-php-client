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

include ('../vendor/autoload.php');

/**
 *
 * getFileContent function
 * function used to download the blob of a file, converted into a PDF file
 * @param String $path contains the path of th file holding the blob
 */
function getFileContent($path = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395') {
    $eurl = explode("/", $path);
    $temp = str_replace(" ", "", end($eurl));
    $client = new \Nuxeo\Automation\Client\NuxeoPhpAutomationClient('http://nuxeo:8080/nuxeo/site/automation');
    $session = $client->getSession('Administrator', 'Administrator');
    $answer = $session->newRequest("Blob.Get")->set('input', 'doc:'. $path)->sendRequest();

    if (!isset($answer) OR $answer == false)
        echo '$answer is not set';
    else {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $temp . '.pdf');
        readfile('tempstream');
    }
}

if (!isset($_POST['data']) OR empty($_POST['data']))
    echo 'error';
else
    getFileContent($_POST['data']);
?>

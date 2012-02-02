<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
?>
<html>
<head>
    <title>B1 test php Client</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <link rel="stylesheet" media="screen" type="text/css" title="Designtab" href="designtab.css"/>
</head>
<body>
Execute a "SELECT * FROM NuxeoDocument WHERE ecm:path = Path" query to Nuxeo<br/>
and print all the document properties.<br/>
fill the path field with a correct Path and the Schema field<br/>
with the type of schema to output (if left blank, print all properties)<br/>

<form action="B1.php" method="post">
    Path<input type="text" name="path"/>
    Schema<input type="text" name="schema"/>
    <input type="submit" value="Submit"/>
</form>
<br/>
<?php
    include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');

function openNuxeoDocumentPropeties($path, $propertiesSchema = '*') {

    $client = new NuxeoPhpAutomationClient('http://localhost:8080/nuxeo/site/automation');

    $session = $client->getNuxeoSession('Administrator', 'Administrator');

    $answer = $session->newRequest("NuxeoDocument.Query")->set('params', 'query', "SELECT * FROM NuxeoDocument WHERE ecm:path = '" . $path . "'")->setSchema($propertiesSchema)->sendRequest();

    $documentsArray = $answer->getNuxeoDocumentList();
    $value = sizeof($documentsArray);
    echo '<table>';
    echo '<tr><TH>uid</TH><TH>Path</TH>
		<TH>Type</TH><TH>State</TH><TH>Title</TH><TH>Property 1</TH>
		<TH>Property 2</TH><TH>Download as PDF</TH>';
    for ($test = 0; $test < $value; $test++) {
        echo '<tr>';
        echo '<td> ' . current($documentsArray)->getUid() . '</td>';
        echo '<td> ' . current($documentsArray)->getPath() . '</td>';
        echo '<td> ' . current($documentsArray)->getType() . '</td>';
        echo '<td> ' . current($documentsArray)->getState() . '</td>';
        echo '<td> ' . current($documentsArray)->getTitle() . '</td>';
        echo '<td> ' . current($documentsArray)->getProperty('dc:description') . '</td>';
        echo '<td> ' . current($documentsArray)->getProperty('dc:creator') . '</td>';
        echo '<td><form id="test" action="../tests/B5bis.php" method="post" >';
        echo '<input type="hidden" name="data" value="' .
             current($documentsArray)->getPath() . '"/>';
        echo '<input type="submit" value="download"/>';
        echo '</form></td></tr>';
        next($documentsArray);
    }
    echo '</table>';
}

if (!isset($_POST['path']) OR empty($_POST['path'])) {
    echo 'path is empty';
}
else {
    if (!isset($_POST['schema']) OR empty($_POST['schema']))
        openNuxeoDocumentPropeties($_POST['path']);
    else
        openNuxeoDocumentPropeties($_POST['path'], $_POST['schema']);
}

?></body>
</html>
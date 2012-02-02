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
    <title>B4 test php Client</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <link rel="stylesheet" media="screen" type="text/css" title="Design" href="design.css"/>
</head>
<body>
Create a file at the path chosen with file path and attach the blob chosen in<br/>
the blob path field to it.<br/>

<form action="B4.php" method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <td>Blob Path</td>
            <td><input type="file" name="blobPath"/></td>
        </tr>
        <tr>
            <td>File Path</td>
            <td><?php

                include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');

                $client = new NuxeoPhpAutomationClient('http://localhost:8080/nuxeo/site/automation');

                $session = $client->getNuxeoSession('Administrator', 'Administrator');

                $answer = $session->newRequest("NuxeoDocument.Query")->set('params', 'query', "SELECT * FROM Workspace")->setSchema($propertiesSchema)->sendRequest();

                $array = $answer->getNuxeoDocumentList();
                $value = sizeof($array);
                echo '<select name="TargetNuxeoDocumentPath">';
                for ($test = 0; $test < $value; $test++) {
                    echo '<option value="' . current($array)->getPath() . '">' . current($array)->getTitle() . '</option>';
                    next($array);
                }
                echo '</select>';
                ?></td>
        </tr>
        <tr>
            <td><input type="submit" value="Submit"/></td>
        </tr>
    </table>
</form><?php

/**
 *
 * AttachBlob function
 *
 * @param String $blob contains the path of the blob to load as an attachment
 * @param String $filePath contains the path of the folder where the fille holding the blob will be created
 * @param String $blobtype contains the type of the blob (given by the $_FILES['blobPath']['type'])
 */
function attachBlob($blob = '../test.txt', $filePath = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395', $blobtype = 'application/binary') {

    //only works on LINUX / MAC
    // We get the name of the file to use it for the name of the document
    $ename = explode("/", $blob);

    $client = new NuxeoPhpAutomationClient('http://localhost:8080/nuxeo/site/automation');

    $session = $client->getNuxeoSession('Administrator', 'Administrator');


    //We create the document that will hold the file
    $answer = $session->newRequest("NuxeoDocument.Create")->set('input', 'doc:' . $filePath)->set('params', 'type', 'File')->set('params', 'name', end($ename))->sendRequest();

    //We upload the file
    $answer = $session->newRequest("Blob.Attach")->set('params', 'document', $answer->getNuxeoDocument(0)->getPath())
            ->loadBlob($blob, $blobtype)
            ->sendRequest();
}

if (!isset($_FILES['blobPath']) AND $_FILES['blobPath']['error'] == 0) {
    echo 'BlobPath is empty';
    exit;
}
if (!isset($_POST['TargetNuxeoDocumentPath']) OR empty($_POST['TargetNuxeoDocumentPath'])) {
    echo 'TargetNuxeoDocumentPath is empty';
    exit;
}
if ((isset($_FILES['blobPath']) && ($_FILES['blobPath']['error'] == UPLOAD_ERR_OK))) {
    $targetPath = '../blobs/';
    if (!is_dir('../blobs'))
        mkdir('../blobs');
    move_uploaded_file($_FILES['blobPath']['tmp_name'], $targetPath . $_FILES['blobPath']['name']);
}

attachBlob($targetPath . $_FILES['blobPath']['name'], $_POST['TargetNuxeoDocumentPath'], $_FILES['blobPath']['type']);
unlink($targetPath . $_FILES['blobPath']['name']);

?></body>
</html>
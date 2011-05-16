<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>B4 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
       	<link rel="stylesheet" media="screen" type="text/css" title="Design" href="design.css" />
    </head>
    <body>
    	Create a file at the path chosen with file path and attach the blob chosen in<br />
    	the blob path field to it.<br />
	    <form action="B4.php" method="post" enctype="multipart/form-data">
			<table>
			    <tr><td>Blob Path</td><td><input type="file" name ="blobPath"/></td></tr>
			    <tr><td>File Path</td><td><?php
	
												include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
												
												$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
												
												$session = $client->getSession('Administrator','Administrator');
												
												$answer = $session->newRequest("Document.Query")->set('params', 'query', "SELECT * FROM Workspace")->setSchema($propertiesSchema)->sendRequest();
												
												$array = $answer->getDocumentList();
												$value = sizeof($array);
												echo '<select name="TargetDocumentPath">';
												for ($test = 0; $test < $value; $test ++){
													echo '<option value="' . current($array)->getPath() . '">' . current($array)->getTitle() . '</option>';
													next($array);
												}
												echo '</select>';
											?></td></tr>
			    <tr><td><input type="submit" value="Submit"/></td></tr>
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
	function attachBlob($blob = '../test.txt', $filePath = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395', $blobtype = 'application/binary'){
		
		//only works on LINUX / MAC
		// We get the name of the file to use it for the name of the document
		$ename = explode("/", $blob);
		
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->getSession('Administrator','Administrator');
		
		
		//We create the document that will hold the file
		$answer = $session->newRequest("Document.Create")->set('input', 'doc:' . $filePath)->set('params', 'type', 'File')->set('params', 'name', end($ename))->sendRequest();
		
		//We upload the file
		$answer = $session->newRequest("Blob.Attach")->set('params', 'document', $answer->getDocument(0)->getPath())
		->loadBlob($blob, $blobtype)
		->sendRequest();
	}
	
	if(!isset($_FILES['blobPath']) AND $_FILES['blobPath']['error'] == 0){
		echo 'BlobPath is empty';
		exit;
	}
	if(!isset($_POST['TargetDocumentPath']) OR empty($_POST['TargetDocumentPath'])){
		echo 'TargetDocumentPath is empty';
		exit;
	}
	if ((isset($_FILES['blobPath'])&&($_FILES['blobPath']['error'] == UPLOAD_ERR_OK))) {    
		$targetPath = '../blobs/';
		if (!is_dir('../blobs'))
			mkdir('../blobs');
		move_uploaded_file($_FILES['blobPath']['tmp_name'], $targetPath.$_FILES['blobPath']['name']);  
	}
	
	attachBlob($targetPath.$_FILES['blobPath']['name'], $_POST['TargetDocumentPath'], $_FILES['blobPath']['type']);
	unlink($targetPath.$_FILES['blobPath']['name']);
	
?></body></html>
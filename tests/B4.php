<?php

	include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
	
	function AttachBlob($blob = '../test.txt', $filePath = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395', $blobtype = 'application/binary'){
		//only works on LINUX / MAC
		$ename = explode("/", $blob);
		
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->GetSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Document.Create")->Set('input', 'doc:' . $filePath)->Set('params', 'type', 'File')->Set('params', 'name', end($ename))->SendRequest();
		
		$answer = $session->NewRequest("Blob.Attach")->Set('params', 'document', $answer->OutputDocument(0)->GetPath())->LoadBlob($blob, $blobtype)->SendRequest();
	}
	
	if(!isset($_FILES['blobPath']) AND $_FILES['blobPath']['error'] == 0){
		echo 'BlobPath est vide';
		exit;
	}
	if(!isset($_POST['DocList']) OR empty($_POST['DocList'])){
		echo 'DocList est vide';
		exit;
	}
	if ((isset($_FILES['blobPath'])&&($_FILES['blobPath']['error'] == UPLOAD_ERR_OK))) {    
		$chemin_destination = '../blobs/';
		move_uploaded_file($_FILES['blobPath']['tmp_name'], $chemin_destination.$_FILES['blobPath']['name']);  
	} 
	AttachBlob($chemin_destination.$_FILES['blobPath']['name'], $_POST['DocList'], $_FILES['blobPath']['type']);
	unlink($chemin_destination.$_FILES['blobPath']['name']);
	
?>
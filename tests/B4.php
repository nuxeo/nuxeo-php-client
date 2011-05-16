<?php

	include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
	
	/**
	 * 
	 * AttachBlob function
	 * 
	 * @param String $blob contains the path of the blob to load as an attachment
	 * @param String $filePath contains the path of the folder where the fille holding the blob will be created
	 * @param String $blobtype contains the type of the blob (given by the $_FILES['blobPath']['type'])
	 */
	function AttachBlob($blob = '../test.txt', $filePath = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395', $blobtype = 'application/binary'){
		
		//only works on LINUX / MAC
		// We get the name of the file to use it for the name of the document
		$ename = explode("/", $blob);
		
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->GetSession('Administrator','Administrator');
		
		
		//We create the document that will hold the file
		$answer = $session->NewRequest("Document.Create")->Set('input', 'doc:' . $filePath)->Set('params', 'type', 'File')->Set('params', 'name', end($ename))->SendRequest();
		
		//We upload the file
		$answer = $session->NewRequest("Blob.Attach")->Set('params', 'document', $answer->GetDocument(0)->GetPath())
		->LoadBlob($blob, $blobtype)
		->SendRequest();
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
		move_uploaded_file($_FILES['blobPath']['tmp_name'], $targetPath.$_FILES['blobPath']['name']);  
	}
	
	AttachBlob($targetPath.$_FILES['blobPath']['name'], $_POST['TargetDocumentPath'], $_FILES['blobPath']['type']);
	unlink($targetPath.$_FILES['blobPath']['name']);
	
?>
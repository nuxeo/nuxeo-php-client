<?php
	include ('../functions/functionList.php');

	function getBlob($path = '/default-domain/workspaces/jkjkj/DocumentCreate.rtf', $blobtype = 'application/binary') {
		
		$eurl = explode("/", $path);
		
		header("Content-type: text/plain");
  		header("Content-Disposition: attachment; filename=".end($eurl));
		
		$client = new phpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->getSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Blob.Get")->Set('input', 'doc: ' . $path)->Send_Request();
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{
			print_r($answer);
		}
		
	}
	
	getBlob();
?>
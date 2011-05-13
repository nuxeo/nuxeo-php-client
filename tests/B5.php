<?php

	include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
	
	function GetBlob($path = '/default-domain/workspaces/jkjkj/test2.rtf', $blobtype = 'application/binary') {
		$eurl = explode("/", $path);
		header("Content-type: text/plain");
  		header("Content-Disposition: attachment; filename=".end($eurl));
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->GetSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Blob.Get")->Set('input', 'doc: ' . $path)->SendRequest();
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{
			print_r($answer);
		}
	}
	
	if(!isset($_POST['path'])){
		echo 'path est vide';
		exit;
	}
	if(!isset($_POST['type']))
		GetBlob($_POST['path']);
	else
		GetBlob($_POST['path'], $_POST['type']);
?>
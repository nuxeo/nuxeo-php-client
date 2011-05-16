<?php

	include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
	
	function GetBlob($path = '/default-domain/workspaces/jkjkj/test2.rtf', $blobtype = 'application/binary') {
		$eurl = explode("/", $path);
		
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->GetSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Blob.Get")->Set('input', 'doc: ' . $path)->SendRequest();
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.end($eurl).'.pdf');
		    readfile('tempstream');
		}
	}
	
	if(!isset($_POST['path'])){
		echo 'path is empty';
		exit;
	}
	if(!isset($_POST['type']))
		GetBlob($_POST['path']);
	else
		GetBlob($_POST['path'], $_POST['type']);
?>
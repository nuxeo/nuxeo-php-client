<?php

	include ('../functions/functionList.php');
	
	function attachBlob($blob = '../test.txt', $filePath = '/default-domain/workspaces/jkjkj/teezeareate.1304514516986', $blobtype = 'application/binary'){
		
		$client = new phpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->getSession('Administrator','Administrator');
	
		$answer = $session->NewRequest("Blob.Attach")->Set('params', 'document', $filePath)->Set('params', 'xpath', 'files:files')->loadBlob($blob, $blobtype)->loadBlob('../test2.txt')->Send_request();
		
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{
			$documents = new Documents($answer);
			$documents->affichage();
		}
	}
	
	attachBlob();
	
?>
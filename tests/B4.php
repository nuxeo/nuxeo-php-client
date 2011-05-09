<?php

	include ('../functions/functionList.php');
	
	function AttachBlob($blob = '../test.txt', $filePath = '/default-domain/workspaces/jkjkj/teezeareate.1304514516986', $blobtype = 'application/binary'){
		
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->GetSession('Administrator','Administrator');
	
		$answer = $session->NewRequest("Blob.Attach")->Set('params', 'document', $filePath)->LoadBlob($blob, $blobtype)->LoadBlob('../test2.txt')->SendRequest();
		
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{
			$documents = new Documents($answer);
			$documents->Output();
		}
	}
	
	AttachBlob();
	
?>
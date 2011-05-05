<?php
	
	include ('../functions/functionList.php');

	
	function getFileContent($path = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395') {
		
		$eurl = explode("/", $path);
		
		header("Content-type: text/plain");
  		header("Content-Disposition: attachment; filename=".end($eurl).'.pdf');
		
		$client = new phpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->getSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Chain.")->Set('context', 'path' . $path)->Send_Request();
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{
			print_r($answer);
		}
		
	}
	
	getFileContent();
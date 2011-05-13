<?php
	
	include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
	
	if (!isset($_POST['a_recup']) OR empty($_POST['a_recup']))
		echo ' ceci est une erreur !';
	
	function getFileContent($path = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395') {
		
		$eurl = explode("/", $path);
		
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->GetSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Chain.getDocContent")->Set('context', 'path', $path)->SendRequest();
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.end($eurl).'.pdf');
		    readfile('tempstream');
		}
	}
	
	getFileContent($_POST['a_recup']);
	echo $_POST['a_recup'];
	//getFileContent('/default-domain/workspaces/jkjkj/test2.rtf');
?>
<?php
	
		include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
	
	/**
	 * 
	 * getFileContent function
	 * function used to download the blob of a file, converted into a PDF file
	 * @param String $path contains the path of th file holding the blob
	 */
	function getFileContent($path = '/default-domain/workspaces/jkjkj/teezeareate.1304515647395') {
		
		$eurl = explode("/", $path);
		
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->getSession('Administrator','Administrator');
		
		$answer = $session->newRequest("Chain.getDocContent")->set('context', 'path', $path)->sendRequest();
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.end($eurl).'.pdf');
		    readfile('tempstream');
		}
	}
		
	if (!isset($_POST['data']) OR empty($_POST['data']))
		echo 'error';
	else
		getFileContent($_POST['data']);
?>
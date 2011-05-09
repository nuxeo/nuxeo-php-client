<?php
	include ('../functions/functionList.php');
	
	//if(!isset($_POST['path'])){
	//	echo 'path est vide';
	//  exit;
	//}
	//if(!isset($_POST['schema'])){
	//	echo 'schema est vide';
	//  exit;
	//}
	
	function openDocumentPropeties($path, $propertiesSchema) {
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->GetSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Document.Query")->Set('params', 'query', "SELECT * FROM Document WHERE ecm:path = '". $path ."'")->SetSchema($propertiesSchema)->SendRequest();
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{
			$documents = new Documents($answer);
			$documents->Output();
		}
	}
	
	//openDocumentPropeties($_POST['path'], $_POST['schema']);
	
?>
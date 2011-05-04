<?php
	include ('./functions/functionList.php');
	
	//if(!isset($_POST['path'])){
	//	echo 'path est vide';
	//  exit;
	//}
	//if(!isset($_POST['schema'])){
	//	echo 'schema est vide';
	//  exit;
	//}
	
	function openDocumentPropeties($path, $propertiesSchema) {
		$client = new phpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->getSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Document.Query")->Set('params', 'query', "SELECT * FROM Document WHERE ecm:path = '". $path ."'")->setSchema($propertiesSchema)->Send_Request();
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{
			$documents = new Documents($answer);
			$documents->affichage();
		}
	}
	
	//openDocumentPropeties($_POST['path'], $_POST['schema']);
	
?>
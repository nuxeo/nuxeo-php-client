<?php

	include ('./functions/functionList.php');
	
	//if(!isset($_POST['research'])){
	//	echo 'path est vide';
	//  exit;
	//}

	function fullTextSearch($research) {
		$client = new phpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->getSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Document.Query")->Set('params', 'query', "SELECT * FROM Document WHERE ecm:fulltext = '". $research ."'")->Send_Request();
		
		if (!isset($answer) OR $answer == false)
			echo '$answer is not set';
		else{
			$documents = new Documents($answer);
			$documents->affichage();
		}
	}
?>
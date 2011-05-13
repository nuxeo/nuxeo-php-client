<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>B2 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
       	<link rel="stylesheet" media="screen" type="text/css" title="Designtab" href="designtab.css" />
    </head>
    <body>
		Execute a SELECT * FROM Document WHERE ecm:path = Path query to nuxeo<br />
    	and print all the document porperties.<br />
    	fill the path field with a correct Path and the Schema field<br />
    	with the type of schema to output (il left blank, print all properties)<br />
	    <form action="B1.php" method="post">
			Path<input type="text" name ="path"/>
			Schema<input type="text" name ="schema"/>
			<input type="submit" value="Envoyer !"/>
	    </form>
	    <br />
<?php
	
	include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
	
	function openDocumentPropeties($path, $propertiesSchema = '*') {

		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
		
		$session = $client->GetSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Document.Query")->Set('params', 'query', "SELECT * FROM Document WHERE ecm:path = '". $path ."'")->SetSchema($propertiesSchema)->SendRequest();
		
		$answer->Output();
	}
	
	//openDocumentPropeties('/default-domain/workspaces/jkjkj/test2.rtf', 'testtype');
	
	if(!isset($_POST['path']) OR empty($_POST['path'])){
		echo 'path is empty';
	}
	else{
		if(!isset($_POST['schema']) OR empty($_POST['schema']))
			openDocumentPropeties($_POST['path']);
		else
			openDocumentPropeties($_POST['path'], $_POST['schema']);
	}
?></body></html>
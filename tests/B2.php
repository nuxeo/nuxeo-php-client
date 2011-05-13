<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>B2 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
       	<link rel="stylesheet" media="screen" type="text/css" title="Designtab" href="designtab.css" />
    </head>
    <body>
    	Execute a SELECT * FROM Document WHERE ecm:fulltext = '". $research ."' query to Nuxeo.
    	<form action="B2.php" method="post">
			Search<input type="text" name ="research"/><br /> <br />
			<input type="submit" value="Envoyer !"/>
	    </form>
	    <br/>

<?php

	include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
	
	function fullTextSearch($research) {

		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->GetSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Document.Query")->Set('params', 'query', "SELECT * FROM Document WHERE ecm:fulltext = '". $research ."'")->SendRequest();
		
		$answer->Output();
	}
	
	if(!isset($_POST['research']) OR empty($_POST['research'])){
		echo 'research is empty';
		fullTextSearch($_POST['research']);
	}
	//fullTextSearch('test');
?></body></html>
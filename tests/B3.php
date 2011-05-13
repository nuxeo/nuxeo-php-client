<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>B3 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
       	<link rel="stylesheet" media="screen" type="text/css" title="Designtab" href="designtab.css" />
    </head>
    <body>
    	Execute a dc:created query to nuxeo. Fill the blank with a date format Y/M/D
    	<form action="B3.php" method="post">
			Date<input type="text" name ="date"/><br /><br />
			<input type="submit" value="Envoyer !"/>
	    </form>
	    <br />

<?php

	include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
	
	function DateSearch($date) {
		$utilities = new Utilities();
		
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
	
		$session = $client->GetSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Document.Query")->Set('params', 'query', "SELECT * FROM Document WHERE dc:created >= DATE '". $utilities->DateConverterPhpToNuxeo($date) ."'")->SendRequest();
		
		$answer->Output();
	}
	
	if(!isset($_POST['date']) OR empty($_POST['date'])){
		echo 'date is empty';
	}else{
		$top = new Utilities();
		
		$date = $top->DateConverterInputToPhp($_POST['date']);
		
		DateSearch($date);
	}
?></body></html>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>B1 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
       	<link rel="stylesheet" media="screen" type="text/css" title="Designtab" href="designtab.css" />
    </head>
    <body>
		Execute a SELECT * FROM Document WHERE ecm:path = Path query to Nuxeo<br />
    	and print all the document porperties.<br />
    	fill the path field with a correct Path and the Schema field<br />
    	with the type of schema to output (if left blank, print all properties)<br />
	    <form action="B1.php" method="post">
			Path<input type="text" name ="path"/>
			Schema<input type="text" name ="schema"/>
			<input type="submit" value="Envoyer !"/>
	    </form>
	    <br />
<?php
	echo 'top';
	include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
	echo 'top';
	
	function openDocumentPropeties($path, $propertiesSchema = '*') {
		
		$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
		
		$session = $client->GetSession('Administrator','Administrator');
		
		$answer = $session->NewRequest("Document.Query")->Set('params', 'query', "SELECT * FROM Document WHERE ecm:path = '". $path ."'")->SetSchema($propertiesSchema)->SendRequest();
		
		$DocumentsArray = $answer->GetDocumentList();
		$value = sizeof($DocumentsArray);
		echo '<table>';
		echo '<tr><TH>uid</TH><TH>Path</TH>
		<TH>Type</TH><TH>State</TH><TH>Title</TH><TH>Property 1</TH>
		<TH>Property 2</TH><TH>Download as PDF</TH>';
		for ($test = 0; $test < $value; $test ++){
			echo '<tr>';
			echo '<td> ' . current($DocumentsArray)->GetUid()  . '</td>';
			echo '<td> ' . current($DocumentsArray)->GetPath()  . '</td>';
			echo '<td> ' . current($DocumentsArray)->GetType()  . '</td>';
			echo '<td> ' . current($DocumentsArray)->GetState()  . '</td>';
			echo '<td> ' . current($DocumentsArray)->GetTitle()  . '</td>';
			echo '<td> ' . current($DocumentsArray)->GetProperty('dc:description')  . '</td>';
			echo '<td> ' . current($DocumentsArray)->GetProperty('dc:creator')  . '</td>';
			echo '<td><form id="test" action="../tests/B5bis.php" method="post" >';
			echo '<input type="hidden" name="data" value="'. 
			current($DocumentsArray)->GetPath(). '"/>';
			echo '<input type="submit" value="download"/>';
			echo '</form></td></tr>';
			next($DocumentsArray);
		}
		echo '</table>';
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
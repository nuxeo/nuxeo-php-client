<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>B4 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
       	<link rel="stylesheet" media="screen" type="text/css" title="Design" href="design.css" />
    </head>
    <body>
    	Create a file at the path chosen with file path and attach the blob chosen in<br />
    	the blob path field to it.<br />
	    <form action="B4.php" method="post" enctype="multipart/form-data">
			<table>
			    <tr><td>Blob Path</td><td><input type="file" name ="blobPath"/></td></tr>
			    <tr><td>File Path</td><td><?php
	
												include ('../NuxeoAutomationClient/NuxeoAutomationAPI.php');
												
												$client = new PhpAutomationClient('http://localhost:8080/nuxeo/site/automation');
												
												$session = $client->GetSession('Administrator','Administrator');
												
												$answer = $session->NewRequest("Document.Query")->Set('params', 'query', "SELECT * FROM Workspace")->SetSchema($propertiesSchema)->SendRequest();
												
												$array = $answer->GetDocumentList();
												$value = sizeof($array);
												echo '<select name="TargetDocumentPath">';
												for ($test = 0; $test < $value; $test ++){
													echo '<option value="' . current($array)->GetPath() . '">' . current($array)->GetTitle() . '</option>';
													next($this->documentsList);
												}
												echo '</select>';
											?></td></tr>
			    <tr><td><input type="submit" value="Envoyer !"/></td></tr>
		    </table>
	    </form>
    </body>
</html>

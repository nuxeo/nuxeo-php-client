<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>B1 test php Client</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
       	<link rel="stylesheet" media="screen" type="text/css" title="Design" href="design.css" />
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
    </body>
</html>

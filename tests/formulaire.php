<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Formulaire d'inscription Nuxeo World</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
       	<link rel="stylesheet" media="screen" type="text/css" title="Design" href="design.css" />
    </head>
    <body>
    <h1> Formulaire d'inscription au Nuxeo World </h1>
    <description> Vous désirez faire une conférence lors du Nuxeo World ? inscrivez vous ici ! </description>
	    <form action="config.php" method="post">
			<table>
			    <tr><td>First Name</td><td><input type="text" name ="firstName"/></td></tr>
			    <tr><td>Last Name</td><td><input type="text" name ="lastName"/></td></tr>
			    <tr><td>E-mail</td><td><input type="text" name ="Mail"/></td></tr>
			    <tr><td>Langue</td><td><input type="text" name ="langue"/></td></tr>
			    <tr><td>Date</td><td><input type="text" name ="date"/></td></tr>
			    <tr><td>heure</td><td><input type="text" name ="heure"/></td></tr>
			    <tr><td>Sujet</td></tr>
			    <tr><td><textarea name ="Sujet" rows = "5" cols = "50" ></textarea></td></tr>
			    <tr><td><input type="submit" value="Envoyer !"/></td></tr>
		    </table>
	    </form>
    </body>
</html>


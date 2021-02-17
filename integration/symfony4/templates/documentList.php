<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document list from Nuxeo</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <div class="jumbotron">
    <h3>Execute a "SELECT * FROM Document WHERE ecm:path = Path"</h3>
    Fill the path field with a correct Path and the Schema field with the type of schema to output (if left blank, print all properties)
  </div>
  <table class="table">
    <caption>Documents</caption>
    <tr>
      <th scope="col">UID</th>
      <th scope="col">Path</th>
      <th scope="col">Type</th>
      <th scope="col">State</th>
      <th scope="col">Title</th>
      <th scope="col">Property 1</th>
      <th scope="col">Property 2</th>
    </tr>
    <?php foreach($documents as $document): /** @var \Nuxeo\Client\Objects\Document */ ?>
        <tr>
            <td><?php echo $document->getUid() ?></td>
            <td><?php echo $document->getPath() ?></td>
            <td><?php echo $document->getType() ?></td>
            <td><?php echo $document->getState() ?></td>
            <td><?php echo $document->getTitle() ?></td>
            <td><?php echo $document->getProperty('dc:description') ?></td>
            <td><?php echo $document->getProperty('dc:creator') ?></td>
        </tr>
    <?php endforeach ?>
  </table>
</div>
</body>
</html>

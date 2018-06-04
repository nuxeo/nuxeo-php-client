<?php
/**
 * (C) Copyright 2018 Nuxeo SA (http://nuxeo.com/) and contributors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once '../vendor/autoload.php';

$documents = null;
$client = new \Nuxeo\Client\NuxeoClient('http://nuxeo:8080/nuxeo', 'Administrator', 'Administrator');

if(!empty($_POST['path'])) {
  $path = $_POST['path'];
  $schema = $_POST['schema'] ?: '*';

  /** @var \Nuxeo\Client\Objects\Documents $documents */
  $documents = $client
    ->schemas($schema)
    ->automation('Document.Query')
    ->param('query', sprintf('SELECT * FROM Document WHERE ecm:path = "%s"', $path))
    ->execute(\Nuxeo\Client\Objects\Documents::class);
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>B1 test php Client</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="samples.css">
</head>
<body>
<div class="container">
  <div class="jumbotron">
    <h3>Execute a "SELECT * FROM Document WHERE ecm:path = Path"</h3>
    Fill the path field with a correct Path and the Schema field with the type of schema to output (if left blank, print all properties)
  </div>
  <form action="" method="post" class="form-inline">
      <div class="form-group">
        <label for="path" class="sr-only" >Path</label>
        <input type="text" name="path" class="form-control" placeholder="Path" <?php echo isset($_POST['path']) ? sprintf('value="%s"', $_POST['path']):'' ?>/>
      </div>
      <div class="form-group">
        <label for="schema" class="sr-only" >Schema</label>
        <input type="text" name="schema" class="form-control" placeholder="Schema" <?php echo isset($_POST['schema']) ? sprintf('value="%s"', $_POST['schema']):'' ?>/>
      </div>
      <button type="submit" class="btn btn-default">Submit</button>
  </form>
  <?php if(null !== $documents): ?>
    <table class="table">
      <tr>
        <th>UID</th>
        <th>Path</th>
        <th>Type</th>
        <th>State</th>
        <th>Title</th>
        <th>Property 1</th>
        <th>Property 2</th>
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
  <?php endif ?>
</div>
</body>
</html>

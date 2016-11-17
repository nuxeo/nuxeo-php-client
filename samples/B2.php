<?php
/**
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 *
 * Contributors:
 *     Pierre-Gildas MILLON <pgmillon@nuxeo.com>
 */

require_once '../vendor/autoload.php';

$documents = null;
$client = new \Nuxeo\Client\Api\NuxeoClient('http://nuxeo:8080/nuxeo', 'Administrator', 'Administrator');

if(!empty($_POST['q'])) {
    $query = $_POST['q'];

    /** @var \Nuxeo\Client\Api\Objects\Documents $documents */
    $documents = $client
      ->automation('Document.Query')
      ->param('query', sprintf('SELECT * FROM Document WHERE ecm:fulltext = "%s"', $query))
      ->execute(\Nuxeo\Client\Api\Objects\Documents::className);
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>B2 test php Client</title>
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
    <h3>Execute a SELECT * FROM Document WHERE ecm:fulltext = '". $query ."'</h3>
  </div>
  <form action="" method="post" class="form-inline">
    <div class="form-group">
      <label for="q" class="sr-only">Query</label>
      <input type="text" name="q" class="form-control" placeholder="Query" <?php echo isset($_POST['q']) ? sprintf('value="%s"', $_POST['q']):'' ?>/>
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
      </tr>
      <?php foreach($documents->getDocuments() as $document): /** @var \Nuxeo\Client\Api\Objects\Document */ ?>
        <tr>
          <td><?php echo $document->getUid() ?></td>
          <td><?php echo $document->getPath() ?></td>
          <td><?php echo $document->getType() ?></td>
          <td><?php echo $document->getState() ?></td>
          <td><?php echo $document->getTitle() ?></td>
        </tr>
      <?php endforeach ?>
    </table>
  <?php endif ?>

</div>
</body>
</html>

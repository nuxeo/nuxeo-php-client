<?php
/**
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the GNU Lesser General Public License
 * (LGPL) version 2.1 which accompanies this distribution, and is available at
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * Contributors:
 *     Pierre-Gildas MILLON <pgmillon@nuxeo.com>
 */

require_once '../vendor/autoload.php';

$documents = null;
$client = new \Nuxeo\Client\Api\NuxeoClient('http://nuxeo:8080/nuxeo', 'Administrator', 'Administrator');

if(!empty($_POST['date'])) {
    $date = DateTime::createFromFormat('Y/m/d', $_POST['date']);

    /** @var \Nuxeo\Client\Api\Objects\Documents $documents */
    $documents = $client
      ->automation('Document.Query')
      ->param('query', sprintf('SELECT * FROM Document WHERE dc:created >= DATE "%s"', date_format($date, 'Y-m-d')))
      ->execute(\Nuxeo\Client\Api\Objects\Documents::className);
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>B3 test php Client</title>
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
    <h3>Execute a dc:created query</h3>
    <p>Fill the blank with a date format Y/M/D</p>
  </div>
  <form action="" method="post" class="form-inline">
    <div class="form-group">
      <label for="date" class="sr-only">Date</label>
      <input type="text" name="date" class="form-control" placeholder="Date" <?php echo isset($_POST['date']) ? sprintf('value="%s"', $_POST['date']) : '' ?>/>
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
  </form>
</div>
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
</body>
</html>

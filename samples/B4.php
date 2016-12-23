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

$blobTempStorage = __DIR__ . DIRECTORY_SEPARATOR . 'blobs';

if(!@mkdir($blobTempStorage) && !is_dir($blobTempStorage)) {
    throw new \Symfony\Component\HttpFoundation\File\Exception\FileException(sprintf('Could not create %s: ', $blobTempStorage));
}

/** @var \Nuxeo\Client\Api\Objects\Documents $availablePaths */
$availablePaths = $client
  ->automation('Document.Query')
  ->param('query', 'SELECT * FROM Workspace')
  ->execute(\Nuxeo\Client\Api\Objects\Documents::className);

$httpRequest = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$document = null;

if($httpRequest->files->has('blob') && $httpRequest->request->has('path')) {
    $path = $httpRequest->get('path');
    $uploadedBlob = $httpRequest->files->get('blob');
    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedBlob */

    $blob = $uploadedBlob->move($blobTempStorage, $uploadedBlob->getClientOriginalName());

    try {
        /** @var \Nuxeo\Client\Api\Objects\Document $document */
        $document = $client->automation('Document.Create')
          ->input('doc:' . $path)
          ->params(array(
            'type' => 'File',
            'name' => $blob->getFilename(),
            'properties' => 'dc:title=' . $blob->getFilename()
          ))
          ->execute(\Nuxeo\Client\Api\Objects\Document::className);
    } catch(\Nuxeo\Client\Internals\Spi\NuxeoClientException $ex) {
        throw new RuntimeException(sprintf('Could not create Document %s: ' . $ex->getMessage(), $blob->getFilename()));
    }

    try {
        if(null !== $document) {
            $client->automation('Blob.Attach')
              ->input(\Nuxeo\Client\Api\Objects\Blob\Blob::fromFile($blob->getPathname(), $blob->getMimeType()))
              ->param('document', $document->getPath())
              ->execute(\Nuxeo\Client\Api\Objects\Blob\Blob::className);
        }
    } catch(\Nuxeo\Client\Internals\Spi\NuxeoClientException $ex) {
        throw new RuntimeException('Could not attach blob to document: ' . $ex->getMessage());
    }
}

?>
<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>B4 test php Client</title>
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
        <h3>Create a file at the path chosen with file path and attach the blob chosen in the blob path field to it</h3>
    </div>
    <form action="" method="post" enctype="multipart/form-data" class="form-inline">
      <div class="form-group">
        <input type="file" name="blob" />
      </div>
      <div class="form-group">
        <label for="path">Path</label>
        <select name="path" class="form-control">
          <?php foreach($availablePaths->getDocuments() as $path) {
            /** @var \Nuxeo\Client\Api\Objects\Document $path */
            printf('<option value="%s">%s</option>', $path->getPath(), $path->getTitle());
          }
          ?>
        </select>
      </div>
      <button type="submit" class="btn btn-default">Submit</button>
    </form>
    <?php if(null !== $document): ?>
      <table class="table">
        <tr>
          <th>UID</th>
          <th>Path</th>
          <th>Type</th>
          <th>State</th>
          <th>Title</th>
          <th>Download</th>
        </tr>
        <tr>
          <td><?php echo $document->getUid() ?></td>
          <td><?php echo $document->getPath() ?></td>
          <td><?php echo $document->getType() ?></td>
          <td><?php echo $document->getState() ?></td>
          <td><?php echo $document->getTitle() ?></td>
          <td>
            <form action="B5.php" method="post">
              <input type="hidden" name="path" value="<?php echo $document->getPath() ?>" />
              <button type="submit" class="btn btn-default">Download</button>
            </form>
          </td>
        </tr>
      </table>
    <?php endif ?>
</div>
</body>
</html>

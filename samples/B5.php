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

$client = new \Nuxeo\Client\Api\NuxeoClient('http://nuxeo:8080/nuxeo', 'Administrator', 'Administrator');

if(!empty($_POST['path'])) {
    $path = $_POST['path'];

    try {
        /** @var \Nuxeo\Client\Api\Objects\Blob $blob */
        $blob = $client
          ->automation('Blob.Get')
          ->input('doc:' . $path)
          ->execute(\Nuxeo\Client\Api\Objects\Blob::className);

        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($blob->getFile());
        $response->setContentDisposition(
          \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT,
          $blob->getFilename()
        );

        $response->prepare(\Symfony\Component\HttpFoundation\Request::createFromGlobals())->send();
    } catch(\Nuxeo\Client\Internals\Spi\NuxeoClientException $ex) {
        throw new RuntimeException(sprintf('Could not fetch blob of %s: ', $path) . $ex->getMessage());
    }
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>B5 test php Client</title>
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
        <h3>Download the blob attached to a document</h3>
    </div>
    <form action="" method="post" class="form-inline">
        <div class="form-group">
            <label for="path" class="sr-only">Path</label>
            <input type="text" name="path" class="form-control" placeholder="Path"/>
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
    </form>
</div>
</body>
</html>
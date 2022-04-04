<?php

use Nuxeo\Client\Constants;
use Nuxeo\Client\NuxeoClient;
use Nuxeo\Client\Objects\Blob\Blobs;

require_once '../vendor/autoload.php';

$client = new NuxeoClient('http://localhost:8080/nuxeo', 'Administrator', 'Administrator');

/** @var Blobs $blobs */
$blobs = $client->automation('Blob.GetList')
  ->input('doc:bdd062b8-db1d-465c-b760-57dd5e68b272')
  ->execute(Blobs::class);


$read = $blobs->getBlobs()[0]->getStream();
$dest = fopen('test.png', 'wb');
stream_copy_to_stream($read->detach(), $dest);

fclose($dest);

echo "Done\n";

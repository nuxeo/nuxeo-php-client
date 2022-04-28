<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Response;
use Nuxeo\Client\Constants;
use Nuxeo\Client\Tests\Client as NuxeoClient;
use Illuminate\Routing\Controller;

class NuxeoController extends Controller {
  public function index() {
    $client = new NuxeoClient;

    $file = new \SplFileObject('document-list.json', 'rb', true);
    $client->addResponse(new Response(200, ['Content-Type' => Constants::CONTENT_TYPE_JSON], file_get_contents($file->getRealPath())));

    return view('nuxeo', [
      'documents' => $client->repository()
        ->query('SELECT * FROM Document WHERE dc:title IS NOT NULL AND ecm:path STARTSWITH "/default-domain"')
    ]);
  }
}
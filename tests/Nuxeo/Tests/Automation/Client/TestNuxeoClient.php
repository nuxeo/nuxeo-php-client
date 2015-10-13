<?php
/**
 *
 * @author Pierre-Gildas MILLON <pgmillon@gmail.com>
 */

namespace Nuxeo\Tests\Automation\Client;

use Nuxeo\Automation\Client\NuxeoPhpAutomationClient;

class TestNuxeoClient extends \PHPUnit_Framework_TestCase {

  private $LOGIN = "Administrator";
  private $PASSWORD = "Administrator";
  private $URL = "http://127.0.0.1:8080/nuxeo/site/automation";

  public function testGetRequest() {
    $client = new NuxeoPhpAutomationClient($this->URL);
    $session = $client->getSession($this->LOGIN, $this->PASSWORD);
    $request = $session->newRequest("Document.Query");

    $this->assertNotNull($request);
  }

  public function testListDocuments() {
    $client = new NuxeoPhpAutomationClient($this->URL);
    $session = $client->getSession($this->LOGIN, $this->PASSWORD);
    $answer = $session->newRequest("Document.Query")
      ->set('params', 'query', "SELECT * FROM Document")
      ->setSchema("*")
      ->sendRequest();

    $documentsArray = $answer->getDocumentList();

    $size = sizeof($documentsArray);
    $this->assertTrue($size >= 3);

    foreach ($documentsArray as $document) {
      $this->assertNotNull($document->getUid());
      $this->assertNotNull($document->getPath());
      $this->assertNotNull($document->getType());
      $this->assertNotNull($document->getState());
      $this->assertNotNull($document->getTitle());
    }

  }

}
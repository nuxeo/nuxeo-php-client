<?php

require_once('NuxeoAutomationClient/NuxeoAutomationAPI.php');

class NuxeoTest extends PHPUnit_Framework_TestCase {

    private $LOGIN = "Administrator";
    private $PASSWORD = "Administrator";
    private $URL = "http://darkstar:8080/nuxeo/site/automation";

    public function XXXtest1() {
        $client = new PhpAutomationClient($this->URL);
        $session = $client->getSession($this->LOGIN, $this->PASSWORD);
        $answer = $session->newRequest("Document.Query")
                ->set('params', 'query', "SELECT * FROM Document")
                ->setSchema("*")
                ->sendRequest();

        $documentsArray = $answer->getDocumentList();

        $size = sizeof($documentsArray);
        $this->assertTrue($size >= 3);

        for ($i = 0; $i < $size; $i++) {
            echo current($documentsArray)->getPath() . "\n";
            $this->assertNotNull(current($documentsArray)->getUid());
            $this->assertNotNull(current($documentsArray)->getPath());
            $this->assertNotNull(current($documentsArray)->getType());
            $this->assertNotNull(current($documentsArray)->getState());
            $this->assertNotNull(current($documentsArray)->getTitle());
            $this->assertNotNull(current($documentsArray)->getProperty("dc:description"));
            $this->assertNotNull(current($documentsArray)->getProperty("dc:creator"));
            next($documentsArray);
        }
    }

    public function test2() {
        $client = new PhpAutomationClient($this->URL);
        $session = $client->getSession($this->LOGIN, $this->PASSWORD);

        $answer = $session->newRequest("Document.Query")
                ->set('params', 'query', "SELECT * FROM Document WHERE ecm:fulltext = 'caca'")
                ->setSchema("*")
                ->sendRequest();

        $documentsArray = $answer->getDocumentList();

        $size = sizeof($documentsArray);

        for ($i = 0; $i < $size; $i++) {
            $this->assertNotNull(current($documentsArray)->getUid());
            $this->assertNotNull(current($documentsArray)->getPath());
            $this->assertNotNull(current($documentsArray)->getType());
            $this->assertNotNull(current($documentsArray)->getState());
            $this->assertNotNull(current($documentsArray)->getTitle());
            $this->assertNotNull(current($documentsArray)->getProperty("dc:description"));
            $this->assertNotNull(current($documentsArray)->getProperty("dc:creator"));
            next($documentsArray);
        }
    }

}

?>
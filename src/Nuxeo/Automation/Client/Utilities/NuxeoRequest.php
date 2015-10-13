<?php
/*
 * (C) Copyright 2015 Nuxeo SA (http://nuxeo.com/) and contributors.
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

namespace Nuxeo\Automation\Client\Utilities;

use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\RequestInterface;
use Nuxeo\Automation\Client\NuxeoDocuments;

/**
 * Request class
 *
 * Request class contents all the functions needed to initialize a request and send it
 *
 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 */
class NuxeoRequest {

  private $finalRequest;
  private $url;
  private $headers;
  private $method;
  private $iterationNumber;
  private $HEADER_NX_SCHEMAS;
  private $blobList;
  private $X_NXVoidOperation;

  /**
   * @var EntityEnclosingRequest
   */
  private $request;


  public function __construct(RequestInterface $request) {
    $this->request = $request;
    $this->finalRequest = '{}';
    $this->method = 'POST';
    $this->iterationNumber = 0;
    $this->HEADER_NX_SCHEMAS = 'X-NXDocumentProperties:';
    $this->blobList = null;
    $this->X_NXVoidOperation = 'X-NXVoidOperation: true';
  }

  /**
   * This header is used for the blob upload, it's noticing if the blob must be send back to the
   * client. If not used, i might be great to not using it because it will save time and connection
   * capacity
   *
   * @param string $headerValue
   * @return NuxeoRequest
   */
  public function setX_NXVoidOperation($headerValue = '*') {
    $this->X_NXVoidOperation = 'X-NXVoidOperation:' . $headerValue;
    return $this;
  }

  /**
   * @param string $schema
   * @return NuxeoRequest
   */
  public function setSchema($schema = '*') {
    $this->request->addHeader($this->HEADER_NX_SCHEMAS, $schema);
    return $this;
  }

  /**
   * @param string $requestType               Name of the field
   * @param string $requestContentOrVarName   Name of the var or the content of the field
   *                                          in the case of an input field
   * @param mixed $requestVarVallue           Value of the var define in $requestContentTypeOrVarName(if needed)
   * @return NuxeoRequest
   */
  public function set($requestType, $requestContentOrVarName, $requestVarVallue = NULL) {

    if ($requestVarVallue !== NULL) {
      if ($this->iterationNumber === 0) {
        $this->finalRequest = array(
          $requestType => array($requestContentOrVarName => $requestVarVallue)
        );
      } else if ($this->iterationNumber === 1) {
        $this->finalRequest[$requestType] = array($requestContentOrVarName => $requestVarVallue);
      } else if ($this->iterationNumber === 2) {
        $this->finalRequest[$requestType][$requestContentOrVarName] = $requestVarVallue;
      }

      $this->iterationNumber = 2;
    } else {
      if ($this->iterationNumber === 0) {
        $this->finalRequest = array(
          $requestType => $requestContentOrVarName
        );
      } else {
        $this->finalRequest[$requestType] = $requestContentOrVarName;
      }

      if ($this->iterationNumber === 0)
        $this->iterationNumber = 1;
    }

    return $this;
  }

  /**
   * This function is used to send a multipart request (blob + request) to Nuxeo EM, such as the attachBlob request
   * @return string
   */
  private function multiPart() {

    if (sizeof($this->blobList) > 1 AND !isset($this->finalRequest['params']['xpath']))
      $this->finalRequest['params']['xpath'] = 'files:files';

    $this->finalRequest = json_encode($this->finalRequest);
    $this->finalRequest = str_replace('\/', '/', $this->finalRequest);
    $this->headers = array($this->headers, 'Content-ID: request');

    $requestheaders = 'Content-Type: application/json+nxrequest; charset=UTF-8' . "\r\n" .
      'Content-Transfer-Encoding: 8bit' . "\r\n" .
      'Content-ID: request' . "\r\n" .
      'Content-Length:' . strlen($this->finalRequest) . "\r\n" . "\r\n";

    $value = sizeof($this->blobList);

    $boundary = '====Part=' . time() . '=' . (int)rand(0, 1000000000) . '===';

    $data = "--" . $boundary . "\r\n" .
      $requestheaders .
      $this->finalRequest . "\r\n" . "\r\n";

    for ($cpt = 0; $cpt < $value; $cpt++) {
      $data = $data . "--" . $boundary . "\r\n";

      $blobheaders = 'Content-Type:' . $this->blobList[$cpt][1] . "\r\n" .
        'Content-ID: input' . "\r\n" .
        'Content-Transfer-Encoding: binary' . "\r\n" .
        'Content-Disposition: attachment;filename="' . $this->blobList[$cpt][0] . '"' .
        "\r\n" . "\r\n";

      $data = "\r\n" . $data .
        $blobheaders .
        $this->blobList[$cpt][2] . "\r\n";
    }

    $data = $data . "--" . $boundary . "--";

    $final = array('http' => array(
      'method' => 'POST',
      'content' => $data));

    $final['http']['header'] = 'Accept: application/json+nxentity, */*' . "\r\n" .
      'Content-Type: multipart/related;boundary="' . $boundary .
      '";type="application/json+nxrequest";start="request"' .
      "\r\n" . $this->X_NXVoidOperation;

    $final = stream_context_create($final);

    $fp = @fopen($this->url, 'rb', false, $final);

    $answer = @stream_get_contents($fp);
    $answer = json_decode($answer, true);
    return $answer;
  }

  /**
   * Many blobs could be loaded, they will be store in a blob array
   *
   * @param $adresse : contains the path of the file to load
   * @param $contentType : type of the blob content (default : 'application/binary')
   * @return NuxeoRequest
   */
  public function loadBlob($adresse, $contentType = 'application/binary') {
    if (!$this->blobList) {
      $this->blobList = array();
    }
    $eadresse = explode("/", $adresse);

    $fp = fopen($adresse, "r");

    if (!$fp)
      echo 'error loading the file';

    $futurBlob = stream_get_contents($fp);
    $temp = end($eadresse);
    $this->blobList[] = array($temp, $contentType, print_r($futurBlob, true));

    return $this;
  }

  /**
   * This function is used to send any kind of request to Nuxeo EM
   * @return NuxeoDocuments|string
   */
  public function sendRequest() {
    if (!$this->blobList) {
      $content = str_replace('\/', '/', json_encode($this->finalRequest));
      $this->request->setBody($content);

      $answer = '';
      try {
        $response = $this->request->send();
        $answer = $response->getBody(true);
      } catch(RequestException $ex) {
        echo 'Error Server';
      }

      if (null == json_decode($answer, true)) {
        $documents = $answer;
        file_put_contents("tempstream", $answer);
      } else {
        $answer = json_decode($answer, true);
        $documents = new NuxeoDocuments($answer);
      }

      return $documents;
    } else
      return $this->multiPart();
  }

}

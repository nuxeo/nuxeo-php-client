<?php


namespace Nuxeo\Client\Tests\Http\Handler;


use GuzzleHttp\Handler\MockHandler as BaseHandler;
use Psr\Http\Message\RequestInterface;

class MockHandler extends BaseHandler {

  private $requests = [];

  public function __invoke(RequestInterface $request, array $options) {
    $this->requests[] = $request;

    return parent::__invoke($request, $options);
  }

  /**
   * @return array
   */
  public function getRequests() {
    return $this->requests;
  }

}

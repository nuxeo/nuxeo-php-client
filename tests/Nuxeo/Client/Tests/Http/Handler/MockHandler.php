<?php


namespace Nuxeo\Client\Tests\Http\Handler;


use GuzzleHttp\Handler\MockHandler as BaseHandler;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;

class MockHandler extends BaseHandler {

  private $requests = [];

  public function __invoke(RequestInterface $request, array $options): PromiseInterface {
    $this->requests[] = $request;

    return parent::__invoke($request, $options);
  }

  /**
   * @return RequestInterface[]
   */
  public function getRequests(): array {
    return $this->requests;
  }

}

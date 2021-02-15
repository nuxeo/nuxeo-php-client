<?php
/**
 * (C) Copyright 2018 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 */

namespace Nuxeo\Client;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use GuzzleHttp\Exception\GuzzleException;
use Nuxeo\Client\Auth\BasicAuthentication;
use Nuxeo\Client\Marshaller;
use Nuxeo\Client\Objects\NuxeoVersion;
use Nuxeo\Client\Objects\Operation;
use Nuxeo\Client\Objects\Repository;
use Nuxeo\Client\Objects\User\User;
use Nuxeo\Client\Objects\UserManager;
use Nuxeo\Client\Objects\WorkflowManager;
use Nuxeo\Client\Spi\Auth\AuthenticationInterceptor;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\Objects\AbstractConnectable;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;
use function GuzzleHttp\Psr7\stream_for;

AnnotationRegistry::registerLoader('class_exists');

/**
 * Class NuxeoClient
 * @package Nuxeo\Client
 */
class NuxeoClient extends AbstractConnectable {

  /**
   * @var User
   */
  private $currentUser;

  /**
   * @var NuxeoVersion
   */
  private $serverVersion;

  /**
   * @param string $url
   * @param string $username
   * @param string $password
   * @throws NuxeoClientException
   */
  public function __construct($url = 'http://localhost:8080/nuxeo', $username = 'Administrator', $password = 'Administrator') {
    parent::__construct();

    try {
      $this->setBaseUrl($url);
    } catch(\InvalidArgumentException $e) {
      NuxeoClientException::fromPrevious($e);
    }

    $this->setAuthenticationMethod(new BasicAuthentication($username, $password));
  }

  /**
   * @return \Nuxeo\Client\Objects\User\User
   */
  public function connect() {
    if(null === $this->currentUser) {
      $this->currentUser = $this->userManager()->fetchCurrentUser();
    }
    return $this->currentUser;
  }

  /**
   * @return NuxeoVersion
   */
  public function getServerVersion() {
    if(null === $this->serverVersion) {
      $this->serverVersion = NuxeoVersion::fromServer($this);
    }
    return $this->serverVersion;
  }

  /**
   * @see http://explorer.nuxeo.com/nuxeo/site/distribution/current/listOperations List of available operations
   * @param string $operationId
   * @return Operation
   */
  public function automation($operationId = null) {
    return new Operation($this, $operationId);
  }

  /**
   * @return Repository
   */
  public function repository() {
    return new Repository($this);
  }

  /**
   * @return UserManager
   */
  public function userManager() {
    return new UserManager($this);
  }

  /**
   * @return WorkflowManager
   */
  public function workflows() {
    return new WorkflowManager($this);
  }

  /**
   * @param AuthenticationInterceptor $authenticationInterceptor
   * @return NuxeoClient
   */
  public function setAuthenticationMethod(AuthenticationInterceptor $authenticationInterceptor) {
    //TODO: log deprecated
    $this->withAuthentication($authenticationInterceptor);
    return $this;
  }

  /**
   * @param string $url
   * @param array $query
   * @return Response
   * @throws NuxeoClientException
   */
  public function get($url, array $query = array()) {
    $request = $this->createRequest(Request::GET, $url)
      ->withQuery($query);

    try {
      return $this->perform($request);
    } catch(GuzzleException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }
  }

  /**
   * @param string $url
   * @param string $body
   * @param array $files
   * @return Response
   * @throws NuxeoClientException
   */
  public function post($url, $body = null, array $files = array()) {
    $request = $this->createRequest(Request::POST, $url);

    try {
      $request = $request->withBody(stream_for($body));

      foreach($files as $file) {
        $request = $request->addRelatedFile($file);
      }

      return $this->perform($request);
    } catch(GuzzleException $e) {
      throw NuxeoClientException::fromPrevious($e);
    }
  }

  /**
   * @param $request Request
   * @return Response
   * @throws NuxeoClientException
   * @throws GuzzleException
   */
  public function perform($request) {
    $new = $this->applyInterceptors($request);

    return $this->getHttpClient()->send($new, [
      'query' => $new->getQuery(),
      'auth' => $new->getAuth()
    ]);
  }

  /**
   * @return Marshaller\NuxeoConverter
   * @throws AnnotationException
   */
  public function getConverter() {
    //TODO: deprecated
    return (new class($this) extends NuxeoEntity {
      public function __construct($nuxeoClient) {
        parent::__construct(null, $nuxeoClient);
      }
    })->getConverter();
  }

  /**
   * @return Reader
   * @throws AnnotationException
   */
  public function getAnnotationReader() {
    //TODO: deprecated
    return (new class($this) extends NuxeoEntity {
      public function __construct($nuxeoClient) {
        parent::__construct(null, $nuxeoClient);
      }
    })->getAnnotationReader();
  }

  /**
   * @param string $applicationName
   * @param string $deviceId
   * @param string $deviceDescription
   * @param string $permission
   * @param boolean $revoke
   * @return string
   * @throws NuxeoClientException
   */
  public function requestAuthenticationToken($applicationName, $deviceId, $deviceDescription = '', $permission = Constants::SECURITY_READ_WRITE, $revoke = false) {
    $res = $this->get('authentication/token', array(
      'applicationName' => $applicationName,
      'deviceId' => $deviceId,
      'deviceDescription' => $deviceDescription,
      'permission' => $permission, 'revoke' => $revoke
    ), array('allow_redirects' => false));
    if ($res->getStatusCode() > 205) {
      throw new NuxeoClientException($res->getStatusCode());
    }
    return $res->getBody();
  }

}

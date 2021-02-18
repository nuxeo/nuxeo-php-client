<?php
/*
 * (C) Copyright 2021 Nuxeo SA (http://nuxeo.com/) and contributors.
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

namespace App\Controller;


use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use Nuxeo\Client\Auth\OAuth2Authentication;
use Nuxeo\Client\Constants;
use Nuxeo\Client\NuxeoClient;
use Nuxeo\Client\Objects\Documents;
//use Nuxeo\Client\Tests\Client as NuxeoClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class NuxeoController {

  protected const NUXEO_URL = 'http://localhost:9081/nuxeo';

  /**
   * @param string $accessToken
   * @return Documents
   */
  protected function fetchDocuments($accessToken) {
    $client = (new NuxeoClient(self::NUXEO_URL))
      ->withAuthentication(new OAuth2Authentication($accessToken));

//    $file = new \SplFileObject('document-list.json', 'rb', true);
//    $client->addResponse(new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => Constants::CONTENT_TYPE_JSON], file_get_contents($file->getRealPath())));

    return $client->repository()
//      You can also configure authentication at the entity level rather than at the whole client level if needed
//      ->withAuthentication(new OAuth2Authentication($accessToken))
      ->query('SELECT * FROM Document WHERE dc:title IS NOT NULL AND ecm:path STARTSWITH "/default-domain"');
  }

  protected function fetchUsername($accessToken) {
    $client = (new NuxeoClient(self::NUXEO_URL))
      ->withAuthentication(new OAuth2Authentication($accessToken));

    return $client->connect()->getUsername();
  }

  protected function getOAuthProvider(): GenericProvider {
    return new GenericProvider([
      'clientId' => 'sfNuxeoApp',
      'redirectUri' => 'http://localhost:8080/login',
      'urlAuthorize' => self::NUXEO_URL.'/oauth2/authorize',
      'urlAccessToken' => self::NUXEO_URL.'/oauth2/token',
      'urlResourceOwnerDetails' => self::NUXEO_URL.'/oauth2/resource',
    ]);
  }

  /**
   * @param Request $request
   * @return Response
   */
  public function index(Request $request): Response {
    $authenticated = false;
    $username = null;
    $documents = [];

    if($token = $request->getSession()->get('oauth2token')) {
      /** @var AccessToken $token */
      $authenticated = !$token->hasExpired();
    }

    if($authenticated) {
      $documents = $this->fetchDocuments($token);
      $username = $this->fetchUsername($token);
    }

    ob_start();
      require __DIR__.'/../../templates/index.php';

    return new Response(ob_get_clean());
  }

  /**
   * @param Request $request
   * @return Response
   */
  public function login(Request $request): RedirectResponse {
    /** @var Session $session */
    $session = $request->getSession();
    $provider = $this->getOAuthProvider();

    if($code = $request->query->get('code')) {
      if($session->getFlashBag()->get('oauth2state', ['invalid'])[0] === $request->query->get('state')) {
        $session->set('oauth2token', $provider->getAccessToken('authorization_code', ['code' => $code]));

        return new RedirectResponse('/');
      }
      throw new AccessDeniedHttpException();
    }

    $redirectUrl = $provider->getAuthorizationUrl();
    $session->getFlashBag()->set('oauth2state', $provider->getState());
    return new RedirectResponse($redirectUrl);
  }

}

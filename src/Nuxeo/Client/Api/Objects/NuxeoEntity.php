<?php
/**
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
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

namespace Nuxeo\Client\Api\Objects;


use Guzzle\Http\Message\Response;
use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\NuxeoClient;
use Nuxeo\Client\Internals\Spi\ClassCastException;
use Nuxeo\Client\Internals\Util\IOUtils;

abstract class NuxeoEntity {

  const className = __CLASS__;

  /**
   * @Serializer\SerializedName("entity-type")
   * @Serializer\Type("string")
   */
  private $entityType;

  /**
   * @var NuxeoClient
   * @Serializer\Exclude()
   */
  protected $nuxeoClient;

  /**
   * @Serializer\SerializedName("repository")
   * @Serializer\Type("string")
   */
  private $repositoryName;

  /**
   * NuxeoEntity constructor.
   * @param $entityType
   * @param NuxeoClient $nuxeoClient
   */
  public function __construct($entityType, $nuxeoClient=null) {
    $this->entityType = $entityType;
    $this->nuxeoClient = $nuxeoClient;
  }

  /**
   * @param Response $response
   * @param string $clazz
   * @return mixed
   * @throws ClassCastException
   */
  protected function computeResponse($response, $clazz) {
    if(false === (
        $response->isContentType(Constants::CONTENT_TYPE_JSON) ||
        $response->isContentType(Constants::CONTENT_TYPE_JSON_NXENTITY))) {

      if(Blob::className !== $clazz) {
        throw new ClassCastException(sprintf('Cannot cast %s as %s', Blob::className, $clazz));
      }

      return new Blob(IOUtils::copyToTempFile($response->getBody()->getStream()), $response->getBody()->getContentType());
    }
    $body = $response->getBody(true);

    return $this->nuxeoClient->getConverter()->read($body, $clazz);
  }

}
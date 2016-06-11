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

/**
 *
 * @author Pierre-Gildas MILLON <pgmillon@gmail.com>
 */

namespace Nuxeo\Client\Api\Objects;


use Guzzle\Http\Message\Response;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Api\NuxeoClient;
use Nuxeo\Client\Internals\Spi\ClassCastException;
use Nuxeo\Client\Internals\Util\IOUtils;

abstract class NuxeoEntity {

  /**
   * @Serializer\SerializedName("entity-type")
   * @Serializer\Type("string")
   */
  private $entityType;

  /**
   * @var NuxeoClient
   */
  protected $nuxeoClient;

  /**
   * @Serializer\SerializedName("repository")
   * @Serializer\Type("string")
   */
  private $repositoryName;

  protected $serializer;

  /**
   * NuxeoEntity constructor.
   * @param $entityType
   * @param NuxeoClient $nuxeoClient
   */
  public function __construct($entityType, $nuxeoClient=null) {
    $this->entityType = $entityType;
    $this->nuxeoClient = $nuxeoClient;
    $this->serializer = SerializerBuilder::create()
      ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
      ->build();
  }

  /**
   * @param Response $response
   * @param string $clazz
   * @return mixed
   */
  protected function computeResponse($response, $clazz) {
    if(false === (
        $response->isContentType(Constants::CONTENT_TYPE_JSON) ||
        $response->isContentType(Constants::CONTENT_TYPE_JSON_NXENTITY))) {

      if(Blob::class !== $clazz) {
        throw new ClassCastException(sprintf("Cannot cast %s as %s", Blob::class, $clazz));
      }


      return new Blob(IOUtils::copyToTempFile($response->getBody()->getStream()), $response->getBody()->getContentType());
    }
    $body = $response->getBody(true);
    $responseObj = $this->serializer->deserialize($body, $clazz, 'json');

    return $responseObj;
  }

}
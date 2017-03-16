<?php
/**
 * (C) Copyright 2017 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 *
 */

namespace Nuxeo\Client\Api\Objects;


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Api\Constants;
use Nuxeo\Client\Internals\Spi\Annotations\DELETE;
use Nuxeo\Client\Internals\Spi\Annotations\GET;
use Nuxeo\Client\Internals\Spi\Annotations\POST;
use Nuxeo\Client\Internals\Spi\Annotations\PUT;
use Nuxeo\Client\Internals\Spi\ClassCastException;
use Nuxeo\Client\Internals\Spi\NuxeoClientException;
use Nuxeo\Client\Internals\Spi\Objects\NuxeoEntity;


class Repository extends NuxeoEntity {

  /**
   * Repository constructor.
   * @param $nuxeoClient
   */
  public function __construct($nuxeoClient) {
    parent::__construct(Constants::ENTITY_TYPE_DOCUMENT, $nuxeoClient);
  }

  /**
   * @GET("path")
   * @param $repositoryName
   * @param string $type
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchDocumentRoot($repositoryName = null, $type = null) {
    if(null !== $repositoryName) {
      return $this->fetchDocumentRootWithRepositoryName($repositoryName);
    }
    return $this->getResponse($type);
  }

  /**
   * @GET("repo/{repositoryName}/path")
   * @param $repositoryName
   * @param string $type
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  protected function fetchDocumentRootWithRepositoryName($repositoryName, $type = null) {
    return $this->getResponse($type);
  }

  /**
   * @GET("path{docPath}")
   * @param string $docPath
   * @param string $repositoryName
   * @param string $type
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchDocumentByPath($docPath, $repositoryName = null, $type = null) {
    if(null !== $repositoryName) {
      return $this->fetchDocumentByPathWithRepositoryName($docPath, $repositoryName, $type);
    }
    return $this->getResponse($type);
  }

  /**
   * @GET("repo/{repositoryName}/path{docPath}")
   * @param string $docPath
   * @param $repositoryName
   * @param null $type
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchDocumentByPathWithRepositoryName($docPath, $repositoryName, $type = null) {
    return $this->getResponse($type);
  }

  /**
   * @GET("id/{docId}")
   * @param string $docId
   * @param string $repositoryName
   * @param string $type
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchDocumentById($docId, $repositoryName = null, $type = null) {
    if(null !== $repositoryName) {
      return $this->fetchDocumentByIdWithRepositoryName($docId, $repositoryName, $type);
    }
    return $this->getResponse($type);
  }

  /**
   * @GET("repo/{repositoryName}/id/{docId}")
   * @param string $docId
   * @param $repositoryName
   * @param string $type
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchDocumentByIdWithRepositoryName($docId, $repositoryName, $type = null) {
    return $this->getResponse($type);
  }

  /**
   * @POST("path{parentPath}")
   * @param string $parentPath
   * @param Document $document
   * @param string $type
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function createDocumentByPath($parentPath, $document, $type = null) {
    return $this->getResponse($type, $document);
  }

  /**
   * @PUT("path{path}")
   * @param $path
   * @param $document
   * @param string $type
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function updateDocumentByPath($path, $document, $type = null) {
    return $this->getResponse($type, $document);
  }

  /**
   * @PUT("id/{docId}")
   * @param $docId
   * @param $document
   * @param string $type
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function updateDocumentById($docId, $document, $type = null) {
    return $this->getResponse($type, $document);
  }

  /**
   * @param Document $document
   * @param string $type
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function updateDocument($document, $type = null) {
    return $this->updateDocumentById($document->getUid(), $document, $type);
  }

  /**
   * @DELETE("path{path}")
   * @param string $path
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function deleteDocumentByPath($path) {
    $this->getResponse();
  }

  /**
   * @DELETE("id/{docId}")
   * @param string $docId
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function deleteDocumentById($docId) {
    $this->getResponse();
  }

  /**
   * @param Document $document
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function deleteDocument($document) {
    $this->deleteDocumentById($document->getUid());
  }

  /**
   * @GET("query?query={query}&pageSize={pageSize}&currentPageIndex={currentPageIndex}&maxResults={maxResults}&sortBy={sortBy}&sortOrder={sortOrder}&queryParams={queryParams}")
   * @param string $query
   * @param int $pageSize
   * @param int $currentPageIndex
   * @param int $maxResults
   * @param string $sortBy
   * @param string $sortOrder
   * @param string $queryParams
   * @return mixed
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function query($query, $pageSize = 0, $currentPageIndex = 0, $maxResults = 200, $sortBy = '', $sortOrder = '', $queryParams = '') {
    return $this->getResponse();
  }

}

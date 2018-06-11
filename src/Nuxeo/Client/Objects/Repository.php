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

namespace Nuxeo\Client\Objects;


use Nuxeo\Client\Constants;
use Nuxeo\Client\Objects\Blob\Blob;
use Nuxeo\Client\Objects\Workflow\Tasks;
use Nuxeo\Client\Objects\Workflow\Workflow;
use Nuxeo\Client\Objects\Workflow\Workflows;
use Nuxeo\Client\Spi\ClassCastException;
use Nuxeo\Client\Spi\Http\Method\DELETE;
use Nuxeo\Client\Spi\Http\Method\GET;
use Nuxeo\Client\Spi\Http\Method\POST;
use Nuxeo\Client\Spi\Http\Method\PUT;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;


class Repository extends NuxeoEntity {

  /**
   * Repository constructor.
   * @param $nuxeoClient
   */
  public function __construct($nuxeoClient) {
    parent::__construct(Constants::ENTITY_TYPE_DOCUMENT, $nuxeoClient);
  }

  //region Documents

  /**
   * @param $repositoryName
   * @param string $type
   * @return Document
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchDocumentRoot($repositoryName = null, $type = null) {
    $path = 'path/';
    if(null !== $repositoryName) {
      $path = 'repo/{repositoryName}/path/';
    }
    return $this->getResponseNew(GET::create($path), $type);
  }

  /**
   * @param string $parentPath
   * @param Document $document
   * @param string $repositoryName
   * @param string $type
   * @return Document
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function createDocumentByPath($parentPath, $document, $repositoryName = null, $type = null) {
    $path = 'path{parentPath}';
    if(null !== $repositoryName) {
      $path = 'repo/{repositoryName}/path{parentPath}';
    }
    return $this->getResponseNew(POST::create($path)
      ->setBody($document),
      $type);
  }

  /**
   * @param string $parentId
   * @param Document $document
   * @param string $repositoryName
   * @param string $type
   * @return Document
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function createDocumentById($parentId, $document, $repositoryName = null, $type = null) {
    $path = 'id/{parentId}';
    if(null !== $repositoryName) {
      $path = 'repo/{repositoryName}/id/{parentId}';
    }
    return $this->getResponseNew(POST::create($path)
      ->setBody($document),
      $type);
  }

  /**
   * @param string $documentPath
   * @param string $repositoryName
   * @param string $type
   * @return Document
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchDocumentByPath($documentPath, $repositoryName = null, $type = null) {
    $path = 'path{documentPath}';
    if(null !== $repositoryName) {
      $path = 'repo/{repositoryName}/path{documentPath}';
    }
    return $this->getResponseNew(GET::create($path), $type);
  }

  /**
   * @param string $documentId
   * @param string $repositoryName
   * @param string $type
   * @return Document
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchDocumentById($documentId, $repositoryName = null, $type = null) {
    $path = 'id/{documentId}';
    if(null !== $repositoryName) {
      $path = 'repo/{repositoryName}/id/{documentId}';
    }
    return $this->getResponseNew(GET::create($path), $type);
  }

  /**
   * @param Document $document
   * @param string $repositoryName
   * @param string $type
   * @return Document
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function updateDocument($document, $repositoryName = null, $type = null) {
    return $this->updateDocumentById($document->getUid(), $document, $repositoryName, $type);
  }

  /**
   * @param $path
   * @param $document
   * @param string $type
   * @return Document
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function updateDocumentByPath($path, $document, $type = null) {
    return $this->getResponseNew(PUT::create('path{path}')
      ->setBody($document),
      $type);
  }

  /**
   * @param string $documentId
   * @param Document $document
   * @param string $repositoryName
   * @param string $type
   * @return Document
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function updateDocumentById($documentId, $document, $repositoryName = null, $type = null) {
    $path = 'id/{documentId}';
    if(null !== $repositoryName) {
      $path = 'repo/{repositoryName}/id/{documentId}';
    }
    return $this->getResponseNew(PUT::create($path)
      ->setBody($document),
      $type);
  }

  /**
   * @param Document $document
   * @param string $repositoryName
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function deleteDocument($document, $repositoryName = null) {
    $this->deleteDocumentById($document->getUid(), $repositoryName);
  }

  /**
   * @param string $path
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function deleteDocumentByPath($path) {
    $this->getResponseNew(DELETE::create('path{path}'));
  }

  /**
   * @param string $documentId
   * @param string $repositoryName
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function deleteDocumentById($documentId, $repositoryName = null) {
    $path = 'id/{documentId}';
    if(null !== $repositoryName) {
      $path = 'repo/{repositoryName}/id/{documentId}';
    }
    $this->getResponseNew(DELETE::create($path));
  }

  //endregion

  //region Query
  /**
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
    $params = [
      'query={query}',
      'pageSize={pageSize}',
      'currentPageIndex={currentPageIndex}',
      'maxResults={maxResults}'
    ];
    if($sortBy) {
      $params[] = 'sortBy={sortBy}';
    }
    if($sortOrder) {
      $params[] = 'sortOrder={sortOrder}';
    }
    if($queryParams) {
      $params[] = 'queryParams={queryParams}';
    }
    return $this->getResponseNew(GET::create('query?' . implode('&', $params)));
  }

  //endregion

  //region Children

  /**
   * @param $parentId
   * @return Documents
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchChildrenById($parentId) {
    return $this->getResponseNew(GET::create('id/{parentId}/@children'),Documents::class);
  }

  //endregion

  //region Blobs

  /**
   * @param $documentId
   * @param $fieldPath
   * @return Blob
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function fetchBlobById($documentId, $fieldPath) {
    return $this->getResponseNew(GET::create('id/{documentId}/@blob/{fieldPath}'), Blob::class);
  }

  //endregion

}

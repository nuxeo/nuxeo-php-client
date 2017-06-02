<?php
/**
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 * Contributors:
 *     Pierre-Gildas MILLON <pgmillon@nuxeo.com>
 */

namespace Nuxeo\Client\Objects;


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Constants;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;

class Documents extends NuxeoEntity {

  const className = __CLASS__;

  /**
   * @var Document[]
   * @Serializer\Type("array<Nuxeo\Client\Objects\Document>")
   * @Serializer\SerializedName("entries")
   */
  private $documents;

  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  private $isPaginable;

  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  private $resultsCount;

  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  private $pageSize;

  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  private $maxPageSize;

  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  private $currentPageSize;

  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  private $currentPageIndex;

  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  private $numberOfPages;

  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  private $isPreviousPageAvailable;

  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  private $isNextPageAvailable;

  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  private $isLastPageAvailable;

  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  private $isSortable;

  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  private $hasError;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $errorMessage;

  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  private $totalSize;

  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  private $pageIndex;

  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  private $pageCount;

  /**
   * Documents constructor.
   */
  public function __construct() {
    parent::__construct(Constants::ENTITY_TYPE_DOCUMENTS);
  }

  public function getDocument($position = 0) {
    return $this->documents[$position];
  }

  public function size() {
    return count($this->documents);
  }

  /**
   * @return Document[]
   */
  public function getDocuments() {
    return $this->documents;
  }

  /**
   * @return boolean
   */
  public function isPaginable() {
    return $this->isPaginable;
  }

  /**
   * @return int
   */
  public function getResultsCount() {
    return $this->resultsCount;
  }

  /**
   * @return int
   */
  public function getPageSize() {
    return $this->pageSize;
  }

  /**
   * @return int
   */
  public function getMaxPageSize() {
    return $this->maxPageSize;
  }

  /**
   * @return int
   */
  public function getCurrentPageSize() {
    return $this->currentPageSize;
  }

  /**
   * @return int
   */
  public function getCurrentPageIndex() {
    return $this->currentPageIndex;
  }

  /**
   * @return int
   */
  public function getNumberOfPages() {
    return $this->numberOfPages;
  }

  /**
   * @return boolean
   */
  public function isPreviousPageAvailable() {
    return $this->isPreviousPageAvailable;
  }

  /**
   * @return boolean
   */
  public function isNextPageAvailable() {
    return $this->isNextPageAvailable;
  }

  /**
   * @return boolean
   */
  public function isLastPageAvailable() {
    return $this->isLastPageAvailable;
  }

  /**
   * @return boolean
   */
  public function isSortable() {
    return $this->isSortable;
  }

  /**
   * @return boolean
   */
  public function hasError() {
    return $this->hasError;
  }

  /**
   * @return string
   */
  public function getErrorMessage() {
    return $this->errorMessage;
  }

  /**
   * @return int
   */
  public function getTotalSize() {
    return $this->totalSize;
  }

  /**
   * @return int
   */
  public function getPageIndex() {
    return $this->pageIndex;
  }

  /**
   * @return int
   */
  public function getPageCount() {
    return $this->pageCount;
  }

}

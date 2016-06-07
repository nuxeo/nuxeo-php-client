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


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Api\Constants;

class Documents extends NuxeoEntity {

  /**
   * @var Document[]
   * @Serializer\Type("array<Nuxeo\Client\Api\Objects\Document>")
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

  public function getDocument($position) {
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
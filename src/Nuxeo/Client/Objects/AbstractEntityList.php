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


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;

/**
 * @Serializer\Discriminator(field="entity-type", map={
 *   "users": "Nuxeo\Client\Objects\User\Users",
 *   "groups": "Nuxeo\Client\Objects\User\Groups",
 *   "documents": "Nuxeo\Client\Objects\Documents"
 *   })
 */
abstract class AbstractEntityList extends NuxeoEntity implements \ArrayAccess, \Iterator, \Countable {

  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  protected $isPaginable;
  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  protected $currentPageSize;
  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  protected $pageCount;
  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  protected $maxPageSize;
  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $errorMessage;
  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  protected $pageSize;
  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  protected $isSortable;
  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  protected $totalSize;
  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  protected $numberOfPages;
  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  protected $pageIndex;
  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  protected $resultsCount;
  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  protected $isNextPageAvailable;
  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  protected $isLastPageAvailable;
  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  protected $hasError;
  /**
   * @var integer
   * @Serializer\Type("integer")
   */
  protected $currentPageIndex;
  /**
   * @var boolean
   * @Serializer\Type("boolean")
   */
  protected $isPreviousPageAvailable;

  /**
   * @return array
   */
  abstract protected function &getEntries();

  public function offsetExists($offset) {
    return isset($this->getEntries()[$offset]);
  }

  public function offsetGet($offset) {
    return $this->getEntries()[$offset]->reconnectWith($this->getNuxeoClient());
  }

  public function offsetSet($offset, $value) {
    $this->getEntries()[$offset] = $value;
  }

  public function offsetUnset($offset) {
    unset($this->getEntries()[$offset]);
  }

  public function current() {
    return current($this->getEntries())->reconnectWith($this->getNuxeoClient());
  }

  public function next() {
    return next($this->getEntries());
  }

  public function key() {
    return key($this->getEntries());
  }

  public function valid() {
    return isset($this->getEntries()[$this->key()]);
  }

  public function rewind() {
    return reset($this->getEntries());
  }

  public function count() {
    return count($this->getEntries());
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

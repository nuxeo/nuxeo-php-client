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


use function \is_string;
use Nuxeo\Client\NuxeoClient;
use Nuxeo\Client\Spi\Http\Method\GET;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\Objects\AbstractConnectable;

class NuxeoVersion extends AbstractConnectable {

  public const NUXEO_VERSION_PATTERN = '/(\d+)\.(\d+)(?:HF-(\d+))?(?:-I\d{8}_\d{4})?/';

  public static $LTS_2015;
  public static $LTS_2016;
  public static $LTS_2017;

  /**
   * @var int
   */
  private $major;

  /**
   * @var int
   */
  private $minor;

  /**
   * @var int
   */
  private $hotfix;

  /**
   * @var bool
   */
  private $snapshot;

  /**
   * @param int $major
   * @param int $minor
   * @param int $hotfix
   * @param bool $snapshot
   */
  public function __construct($major = 0, $minor = 0, $hotfix = 0, $snapshot = false) {
    parent::__construct();

    $this->major = $major;
    $this->minor = $minor;
    $this->hotfix = $hotfix;
    $this->snapshot = $snapshot;
  }

  /**
   * @return int
   */
  public function major() {
    return $this->major;
  }

  /**
   * @return int
   */
  public function minor() {
    return $this->minor;
  }

  /**
   * @return int
   */
  public function hotfix() {
    return $this->hotfix;
  }

  /**
   * @return bool
   */
  public function snapshot() {
    return $this->snapshot;
  }

  /**
   * @param string $productVersion
   * @return self
   */
  public function parse($productVersion) {
    if(!preg_match(self::NUXEO_VERSION_PATTERN, $productVersion,$matches)) {
      throw new NuxeoClientException("Input version='$productVersion' doesn't represent a valid Nuxeo server version");
    }
    [$this->major, $this->minor,] = $matches;
    return $this;
  }

  /**
   * @param self|string $version
   * @return bool
   */
  public function gt($version) {
    if(is_string($version)) {
      $version = self::fromString($version);
    }
    $version1 = "$this->major.$this->minor";
    $version2 = "$version->major.$version->minor";

    return version_compare($version1, $version2, '>') ||
      (version_compare($version1, $version2, '=') && $this->hotfix > $version->hotfix);
  }

  /**
   * @param self|string $version
   * @return bool
   */
  public function lt($version) {
    if(is_string($version)) {
      $version = self::fromString($version);
    }
    $version1 = "$this->major.$this->minor";
    $version2 = "$version->major.$version->minor";

    return version_compare($version1, $version2, '<') ||
      (version_compare($version1, $version2, '=') && $this->hotfix < $version->hotfix);
  }

  /**
   * @param self|string $version
   * @return bool
   */
  public function eq($version) {
    if(is_string($version)) {
      $version = self::fromString($version);
    }
    $version1 = "$this->major.$this->minor";
    $version2 = "$version->major.$version->minor";

    return version_compare($version1, $version2, '=') && $this->hotfix === $version->hotfix;
  }

  /**
   * @param self|string $version
   * @return bool
   */
  public function gte($version) {
    return $this->gt($version) || $this->eq($version);
  }

  /**
   * @param self|string $version
   * @return bool
   */
  public function lte($version) {
    return $this->lt($version) || $this->eq($version);
  }

  public function __toString() {
    $version = "$this->major.$this->minor";

    if($this->hotfix !== 0) {
      $version .= "-HF$this->hotfix";
    }
    if($this->snapshot) {
      $version .= '-SNAPSHOT';
    }
    return $version;
  }


  /**
   * @return self
   */
  protected function fetch() {
    $cmis = $this->getResponseNew(GET::create('../../json/cmis'));
    return $this->parse($cmis['default']['productVersion']);
  }

  /**
   * @param NuxeoClient $nuxeoClient
   * @return self
   */
  public static function fromServer($nuxeoClient) {
    $version = new self();
    $version->reconnectWith($nuxeoClient);

    return $version->fetch();
  }

  /**
   * @param string $input
   * @return self
   */
  public static function fromString($input) {
    $version = new self();
    return $version->parse($input);
  }

}

NuxeoVersion::$LTS_2015 = new NuxeoVersion(7, 10);
NuxeoVersion::$LTS_2016 = new NuxeoVersion(8, 10);
NuxeoVersion::$LTS_2017 = new NuxeoVersion(9, 10);

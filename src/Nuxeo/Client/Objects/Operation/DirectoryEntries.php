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
 */

namespace Nuxeo\Client\Objects\Operation;


use Nuxeo\Client\Objects\Directory\DirectoryEntry;

class DirectoryEntries extends \ArrayObject {

  /**
   * @param array $entries
   * @return self
   * @throws \InvalidArgumentException
   */
  public static function fromArray($entries) {
    $cleanEntries = array();

    foreach($entries as $entry) {
      if(!isset($entry['id'], $entry['label'])) {
        throw new \InvalidArgumentException(DirectoryEntry::MSG_ID_LABEL_MANDATORY);
      }
      $cleanEntries[] = $clean = new DirectoryEntry($entry['id'], $entry['label']);

      isset($entry['obsolete']) ? $clean->setObsolete($entry['obsolete']):null;
      isset($entry['ordering']) ? $clean->setOrdering($entry['ordering']):null;
    }

    return new DirectoryEntries($cleanEntries);
  }

}

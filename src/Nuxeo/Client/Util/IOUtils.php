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

namespace Nuxeo\Client\Util;


class IOUtils {

  /**
   * @param resource $in
   * @return \SplFileInfo
   */
  public static function copyToTempFile($in) {
    $fileName = tempnam(sys_get_temp_dir(), 'nx-');
    $out = fopen($fileName, 'w+');
    $originalPos = ftell($in);

    fseek($in, 0);
    stream_copy_to_stream($in, $out, -1, 0);
    fseek($in, $originalPos);

    fclose($out);

    return new \SplFileInfo($fileName);
  }

  /**
   * @param string $in
   * @return \SplFileInfo
   */
  public static function copyStringToTempFile($in) {
    $fileName = tempnam(sys_get_temp_dir(), 'nx-');
    $out = fopen($fileName, 'w+');

    fwrite($out, $in);
    fclose($out);

    return new \SplFileInfo($fileName);
  }

}

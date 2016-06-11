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

namespace Nuxeo\Client\Internals\Util;


use Guzzle\Stream\Stream;

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

}
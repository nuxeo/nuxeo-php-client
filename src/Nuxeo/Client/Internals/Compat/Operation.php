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

namespace Nuxeo\Client\Internals\Compat;


use Nuxeo\Client\Api\Objects\Operation as BaseOperation;
use Nuxeo\Client\Internals\Spi\ClassCastException;
use Nuxeo\Client\Internals\Spi\NoSuchOperationException;
use Nuxeo\Automation\Client\Internals\NuxeoClientException;

class Operation extends BaseOperation  {

  /**
   * @param null $operationId
   * @return \Guzzle\Http\Message\Response
   * @throws ClassCastException
   * @throws NuxeoClientException
   * @throws NoSuchOperationException
   */
  public function doExecute($operationId = null) {
    try {
      return $this->_doExecute($operationId);
    } catch(\Nuxeo\Client\Internals\Spi\NuxeoClientException $ex) {
      throw new NuxeoClientException($ex->getMessage());
    }
  }

  public function computeResponse($response, $clazz) {
    return parent::computeResponse($response, $clazz);
  }

}
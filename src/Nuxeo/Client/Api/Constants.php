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

namespace Nuxeo\Client\Api;


class Constants {

  const API_PATH = 'api/v1/';

  const AUTOMATION_PATH = Constants::API_PATH.'automation/';

  const HEADER_PROPERTIES = 'X-NXProperties';

  const HEADER_VOID_OPERATION = 'X-NXVoidOperation';

  const ENTITY_TYPE_DOCUMENT = 'document';

  const ENTITY_TYPE_DOCUMENTS = 'documents';

  const ENTITY_TYPE_OPERATION = 'operation';

}
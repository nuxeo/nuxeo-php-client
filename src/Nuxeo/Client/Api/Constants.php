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

namespace Nuxeo\Client\Api;


class Constants {

  const API_PATH = 'api/v1/';

  const AUTOMATION_PATH = 'api/v1/automation/';

  const HEADER_PROPERTIES = 'X-NXProperties';

  const HEADER_VOID_OPERATION = 'X-NXVoidOperation';

  const CONTENT_TYPE_JSON = 'application/json';

  const CONTENT_TYPE_JSON_NXENTITY = 'application/json+nxentity';

  const ENTITY_TYPE_DOCUMENT = 'document';

  const ENTITY_TYPE_DOCUMENTS = 'documents';

  const ENTITY_TYPE_OPERATION = 'operation';
  
  const RANDOM_HEADER = 'NX_RD';

  const TOKEN_SEP = ':';

  const TS_HEADER = 'NX_TS';

  const TOKEN_HEADER = 'NX_TOKEN';

  const USER_HEADER = 'NX_USER';  

}

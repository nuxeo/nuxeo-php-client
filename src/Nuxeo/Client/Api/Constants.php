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

  const AUTOMATION_PATH = 'automation/';

  const HEADER_PROPERTIES = 'X-NXProperties';

  const HEADER_VOID_OPERATION = 'X-NXVoidOperation';

  const CONTENT_TYPE_JSON = 'application/json';

  const CONTENT_TYPE_JSON_NXENTITY = 'application/json+nxentity';

  const ENTITY_TYPE_LOG_ENTRY = 'logEntry';

  const ENTITY_TYPE_DOCUMENT = 'document';

  const ENTITY_TYPE_DOCUMENTS = 'documents';

  const ENTITY_TYPE_OPERATION = 'operation';

  const SECURITY_EVERYTHING = 'Everything';

  const SECURITY_RESTRICTED_READ = 'RestrictedRead';

  const SECURITY_READ = 'Read';

  const SECURITY_WRITE = 'Write';

  const SECURITY_READ_WRITE = 'ReadWrite';

  const SECURITY_REMOVE = 'Remove';

  const SECURITY_VERSION = 'Version';

  const SECURITY_READ_VERSION = 'ReadVersion';

  const SECURITY_WRITE_VERSION = 'WriteVersion';

  const SECURITY_BROWSE = 'Browse';

  const SECURITY_WRITE_SECURITY = 'WriteSecurity';

  const SECURITY_READ_SECURITY = 'ReadSecurity';

  const SECURITY_READ_PROPERTIES = 'ReadProperties';

  const SECURITY_WRITE_PROPERTIES = 'WriteProperties';

  const SECURITY_READ_CHILDREN = 'ReadChildren';

  const SECURITY_ADD_CHILDREN = 'AddChildren';

  const SECURITY_REMOVE_CHILDREN = 'RemoveChildren';

  const SECURITY_READ_LIFE_CYCLE = 'ReadLifeCycle';

  const SECURITY_WRITE_LIFE_CYCLE = 'WriteLifeCycle';

  const SECURITY_MANAGE_WORKFLOWS = 'ManageWorkflows';

  const SECURITY_VIEW_WORKLFOW = 'ReviewParticipant';

  const SECURITY_UNLOCK = 'Unlock';

}

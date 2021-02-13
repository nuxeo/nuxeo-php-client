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


use Nuxeo\Client\Spi\Objects\NuxeoEntity;
use function \count;
use Nuxeo\Client\Objects\Workflow\Task;
use Nuxeo\Client\Objects\Workflow\TaskCompletionRequest;
use Nuxeo\Client\Objects\Workflow\Tasks;
use Nuxeo\Client\Objects\Workflow\Workflow;
use Nuxeo\Client\Objects\Workflow\Workflows;
use Nuxeo\Client\Spi\ClassCastException;
use Nuxeo\Client\Spi\Http\Method\DELETE;
use Nuxeo\Client\Spi\Http\Method\GET;
use Nuxeo\Client\Spi\Http\Method\POST;
use Nuxeo\Client\Spi\Http\Method\PUT;
use Nuxeo\Client\Spi\NuxeoClientException;

class WorkflowManager extends NuxeoEntity {

  public function __construct($nuxeoClient) {
    parent::__construct(null, $nuxeoClient);
  }

  /**
   * @return Workflows
   */
  public function fetchModels() {
    return $this->getResponseNew(GET::create('workflowModel'), Workflows::class);
  }

  /**
   * @param string $documentId
   * @param string $repositoryName
   * @return Workflows
   */
  public function fetchWorkflowsById($documentId, $repositoryName = null) {
    $path = 'id/{documentId}/@workflow';
    if(null !== $repositoryName) {
      $path = 'repo/{repositoryName}/id/{documentId}/@workflow';
    }
    return $this->getResponseNew(GET::create($path), Workflows::class);
  }

  /**
   * @param string $documentId
   * @param string $workflowModelName
   * @param string $repositoryName
   * @return Workflow
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function startWorkflowByNameWithDocId($workflowModelName, $documentId, $repositoryName = null) {
    return $this->startWorkflowWithDocId(Workflow::createFromModelName($workflowModelName), $documentId, $repositoryName);
  }

  /**
   * @param Workflow $workflow
   * @param string $documentId
   * @param string $repositoryName
   * @return Workflow
   */
  public function startWorkflowWithDocId($workflow, $documentId, $repositoryName = null) {
    $path = 'id/{documentId}/@workflow';
    if(null !== $repositoryName) {
      $path = 'repo/{repositoryName}/id/{documentId}/@workflow';
    }
    return $this->getResponseNew(POST::create($path)
      ->setBody($workflow), Workflow::class);
  }

  /**
   * @param string $workflowInstanceId
   */
  public function cancelWorkflowById($workflowInstanceId) {
    $this->getResponseNew(DELETE::create('workflow/{workflowInstanceId}'));
  }

  /**
   * @param string $documentId
   * @param string $repositoryName
   * @return Tasks
   */
  public function fetchTasksByDocumentId($documentId, $repositoryName = null) {
    $path = 'id/{documentId}/@tasks';
    if(null !== $repositoryName) {
      $path = 'repo/{repositoryName}/id/{documentId}/@tasks';
    }
    return $this->getResponseNew(GET::create($path), Tasks::class);
  }

  /**
   * @param string $userId
   * @param string $workflowId
   * @param string $workflowModel
   * @return Tasks
   */
  public function fetchTasks($userId = null, $workflowId = null, $workflowModel = null) {
    $params = [];
    if($userId) {
      $params[] = 'userId={userId}';
    }
    if($workflowId) {
      $params[] = 'workflowInstanceId={workflowId}';
    }
    if($workflowModel) {
      $params[] = 'workflowModelName={workflowModel}';
    }
    if(count($params) > 0) {
      return $this->getResponseNew(GET::create('task?' . implode('&', $params)), Tasks::class);
    }
    return $this->getResponseNew(GET::create('task'), Tasks::class);
  }

  /**
   * @param string $taskId
   * @param string $action
   * @param TaskCompletionRequest $completionRequest
   * @return Task
   */
  public function completeTask($taskId, $action, $completionRequest) {
    return $this->getResponseNew(PUT::create('task/{taskId}/{action}')
      ->setBody($completionRequest), Task::class);
  }

  /**
   * @param string $taskId
   * @param string $actors
   * @param string $comment
   * @return Task
   */
  public function reassignTask($taskId, $actors, $comment) {
    return $this->getResponseNew(PUT::create('task/{taskId}/reassign?actors={actors}&comment={comment}'), Task::class);
  }

  /**
   * @param string $taskId
   * @param string $actors
   * @param string $comment
   * @return Task
   */
  public function delegateTask($taskId, $actors, $comment) {
    return $this->getResponseNew(PUT::create('task/{taskId}/delegate?actors={actors}&comment={comment}'), Task::class);
  }

}

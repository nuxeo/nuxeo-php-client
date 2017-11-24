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

namespace Nuxeo\Client\Objects\Workflow;


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;

class Task extends NuxeoEntity {

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $id;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $name;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $workflowInstanceId;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $workflowModelName;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $state;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $directive;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  private $nodeName;

  /**
   * @var \DateTime
   * @Serializer\Type("DateTime<'Y-m-d\TH:i:s.uT', '', 'Y-m-d\TH:i:s.uT'>")
   */
  private $created;

  /**
   * @var \DateTime
   * @Serializer\Type("DateTime<'Y-m-d\TH:i:s.uT', '', 'Y-m-d\TH:i:s.uT'>")
   */
  private $dueDate;

  /**
   * @var string[]
   * @Serializer\Type("array")
   */
  private $comments;

  /**
   * @var array
   * @Serializer\Type("array<array<string, string>>")
   */
  private $targetDocumentIds;

  /**
   * @var array
   * @Serializer\Type("array<array<string, string>>")
   */
  private $actors;

  /**
   * @var TaskVariables
   * @Serializer\Type("Nuxeo\Client\Objects\Workflow\TaskVariables")
   */
  private $variables;

  /**
   * @var TaskInfo
   * @Serializer\Type("Nuxeo\Client\Objects\Workflow\TaskInfo")
   */
  private $taskInfo;

  /**
   * @param string $action
   * @param TaskCompletionRequest $completionRequest
   * @return Task
   */
  public function complete($action, $completionRequest) {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()->workflows()->completeTask($this->getId(), $action, $completionRequest);
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @param string $actors
   * @param string $comment
   * @return Task
   */
  public function reassign($actors, $comment) {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()->workflows()->reassignTask($this->getId(), $actors, $comment);
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @param string $actors
   * @param string $comment
   * @return Task
   */
  public function delegate($actors, $comment) {
    if($this->getNuxeoClient()) {
      return $this->getNuxeoClient()->workflows()->delegateTask($this->getId(), $actors, $comment);
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

  /**
   * @return string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getWorkflowInstanceId() {
    return $this->workflowInstanceId;
  }

  /**
   * @return string
   */
  public function getWorkflowModelName() {
    return $this->workflowModelName;
  }

  /**
   * @return string
   */
  public function getState() {
    return $this->state;
  }

  /**
   * @return string
   */
  public function getDirective() {
    return $this->directive;
  }

  /**
   * @return string
   */
  public function getNodeName() {
    return $this->nodeName;
  }

  /**
   * @return \DateTime
   */
  public function getCreated() {
    return $this->created;
  }

  /**
   * @return \DateTime
   */
  public function getDueDate() {
    return $this->dueDate;
  }

  /**
   * @return string[]
   */
  public function getComments() {
    return $this->comments;
  }

  /**
   * @return array
   */
  public function getDocumentIds() {
    return $this->targetDocumentIds;
  }

  /**
   * @return array
   */
  public function getActors() {
    return $this->actors;
  }

  /**
   * @return TaskVariables
   */
  public function getVariables() {
    return $this->variables;
  }

  /**
   * @return TaskInfo
   */
  public function getTaskInfo() {
    return $this->taskInfo;
  }

}

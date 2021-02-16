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

namespace Nuxeo\Client\FTests;

use Nuxeo\Client\FTests\Framework\TestCase;
use Nuxeo\Client\Objects\Document;
use Nuxeo\Client\Objects\Workflow\Task;
use Nuxeo\Client\Objects\Workflow\TaskCompletionRequest;
use Nuxeo\Client\Spi\NuxeoClientException;

/**
 * @group server
 */
class WorkflowsTest extends TestCase {

  const SERIAL_WORKFLOW_NAME = 'SerialDocumentReview';

  public function testWorkflows() {
    // Create a note
    $document = $this->getClient()
      ->repository()
      ->createDocumentByPath('/', Document::createWithName('note', 'Note'));

    $workflow = $document->startWorkflow(self::SERIAL_WORKFLOW_NAME);

    self::assertEquals(self::SERIAL_WORKFLOW_NAME, $workflow->getWorkflowModelName());
    self::assertEquals('running', $workflow->getState());
    self::assertCount(1, $workflow->getAttachedDocumentIds());
    self::assertEquals($document->getUid(), $workflow->getAttachedDocumentIds()[0]);

    self::assertNotNull($document->fetchWorkflows());
    $tasks = $this->getClient()->workflows()->fetchTasks(self::LOGIN);
    self::assertCount(1, $tasks);

    /** @var Task $task */
    $task = $tasks[0];
    $task->complete('start_review', (new TaskCompletionRequest($task))->setVariables([
      'comment' => 'comment',
      'participants' => ['user:Administrator'],
      'validationOrReview' => 'simpleReview'
    ]));

    $workflow->cancel();
    try {
      $workflow->cancel();
      self::fail('you should not be able to cancel this workflow twice');
    } catch(NuxeoClientException $e) {
      self::assertEquals(500, $e->getCode());
    }
  }

}

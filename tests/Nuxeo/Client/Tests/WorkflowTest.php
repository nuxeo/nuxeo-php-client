<?php


namespace Nuxeo\Client\Tests;


use Nuxeo\Client\Objects\Document;
use Nuxeo\Client\Objects\Workflow\Task;
use Nuxeo\Client\Objects\Workflow\TaskCompletionRequest;
use Nuxeo\Client\Objects\Workflow\Workflow;
use Nuxeo\Client\Spi\Http\Method\POST;
use Nuxeo\Client\Tests\Framework\TestCase;

class WorkflowTest extends TestCase {

  /**
   * @var \Nuxeo\Client\Objects\Document
   */
  private $document;

  const WORKFLOW_MODEL = 'ParallelDocumentReview';

  protected function setUp() {
    parent::setUp();

    $document = Document::create()->setType('note')->setName('note');

    $this->getClient()->addResponse($this->createJsonResponseFromFile('document.json'));

    $this->document = $this->getClient()
      ->repository()
      ->createDocumentByPath('/', $document);
  }

  public function testListWorkflowModels() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile('workflowModels.json'));

    $models = $this->getClient()
      ->workflows()
      ->fetchModels();

    $this->assertCount(2, $models);
  }

  public function testListCurrentUserTasks() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile('tasks.json'));

    $tasks = $this->getClient()
      ->workflows()
      ->fetchTasks();

    $this->assertRequestPathMatches($this->getClient(), 'task');
    $this->assertCount(1, $tasks);

    /** @var Task $task */
    $task = $tasks[0];
    $this->assertEquals('SerialDocumentReview', $task->getWorkflowModelName());
    $this->assertEquals('opened', $task->getState());
    $this->assertCount(2, $task->getVariables()->getParticipants());
    $this->assertEquals('2015-02-24 23:12:53 +00:00', $task->getCreated()->format('Y-m-d H:i:s P'));
    $this->assertEquals('2015-03-01 23:12:53 +00:00', $task->getDueDate()->format('Y-m-d H:i:s P'));

    $infos = $task->getTaskInfo();
    $this->assertCount(2, $infos->getActions());
    $this->assertEquals('reject', $infos->getActions()[0]->getName());
    $this->assertEquals('validate', $infos->getActions()[1]->getName());
  }

  public function testListTasksFilter() {
    $this->getClient()->addResponse($this->createResponse());

    $userId = 'john';
    $workflowId = '05fd04ca-9f4f-42e8-89f1-5e529477f8da';
    $workflowModel = 'SerialDocumentReview';

    $this->getClient()
      ->workflows()
      ->fetchTasks($userId, $workflowId, $workflowModel);

    $this->assertStringMatchesFormat(
      "%s/task?userId=$userId&workflowInstanceId=$workflowId&workflowModelName=$workflowModel",
      (string) $this->getClient()->getRequest()->getUri());
  }

  public function testListDocumentWorkflows() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile('documentWorkflows.json'));
    $workflows = $this->document->fetchWorkflows();

    $this->assertCount(1, $workflows);

    $this->assertEquals(self::WORKFLOW_MODEL, $workflows[0]->getWorkflowModelName());
  }

  public function testListDocumentTasks() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile('tasks.json'));

    $tasks = $this->document->fetchTasks();

    $this->assertStringMatchesFormat(
      '%s/id/%s/@tasks',
      (string) $this->getClient()->getRequest()->getUri());
    $this->assertCount(1, $tasks);
  }

  public function testStartDocumentWorkflow() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile('documentWorkflow.json'));
    $this->document->startWorkflow(self::WORKFLOW_MODEL);

    $this->assertStringMatchesFormat('{"entity-type":"workflow","workflowModelName":"%s"}', (string) $this->getClient()
      ->getRequest(1)
      ->getBody());
  }

  public function testCancelWorkflow() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile('documentWorkflow.json'));
    $workfow = $this->document->startWorkflow(self::WORKFLOW_MODEL);

    $this->getClient()->addResponse($this->createResponse());
    $workfow->cancel();

    $this->assertContains('workflow/'.$workfow->getId(), (string) $this->getClient()->getRequest()->getUri());
    $this->assertEquals('DELETE', $this->getClient()->getRequest()->getMethod());
  }

  public function testCompleteTask() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile('tasks.json'));
    $this->getClient()->addResponse($this->createResponse());

    /** @var Task $task */
    $task = $this->document->fetchTasks()[0];
    $completionRequest = (new TaskCompletionRequest($task))
      ->setVariables([
        'comment' => 'comment',
        'validationOrReview' => 'simpleReview',
        'participants' => 'user:neo_27c7102345ee79574e713045836e62a3'
      ]);

    $action = 'start_review';
    $task->complete($action, $completionRequest);

    $this->assertStringMatchesFormat(
      "%s/task/%s/$action",
      (string) $this->getClient()->getRequest()->getUri());
    $this->assertEquals('PUT', $this->getClient()->getRequest()->getMethod());
  }

  public function testReassignTask() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile('tasks.json'));
    $this->getClient()->addResponse($this->createResponse());

    /** @var Task $task */
    $task = $this->document->fetchTasks()[0];
    $actors = 'Administrator';

    $task->reassign($actors, 'some comment');

    $this->assertStringMatchesFormat(
      "%s/task/%s/reassign?actors=$actors&comment=%s",
      (string) $this->getClient()->getRequest()->getUri());
    $this->assertEquals('PUT', $this->getClient()->getRequest()->getMethod());
  }


  public function testDelegateTask() {
    $this->getClient()->addResponse($this->createJsonResponseFromFile('tasks.json'));
    $this->getClient()->addResponse($this->createResponse());

    /** @var Task $task */
    $task = $this->document->fetchTasks()[0];
    $actors = 'Administrator';

    $task->delegate($actors, 'some comment');

    $this->assertStringMatchesFormat(
      "%s/task/%s/delegate?actors=$actors&comment=%s",
      (string) $this->getClient()->getRequest()->getUri());
    $this->assertEquals('PUT', $this->getClient()->getRequest()->getMethod());
  }

}

<?php


namespace Nuxeo\Client\Objects\Workflow;


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Constants;
use Nuxeo\Client\Spi\ClassCastException;
use Nuxeo\Client\Spi\NuxeoClientException;
use Nuxeo\Client\Spi\Objects\NuxeoEntity;

class Workflow extends NuxeoEntity {

  public function __construct() {
    parent::__construct(Constants::ENTITY_TYPE_WORKFLOW);
  }

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $id;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $name;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $title;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $state;

  /**
   * @var array
   * @Serializer\Type("array<array<string, string>>")
   */
  protected $attachedDocumentIds;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $workflowModelName;

  /**
   * @var string
   * @Serializer\Type("string")
   */
  protected $graphResource;

  /**
   * @param string $modelName
   * @return Workflow
   */
  public static function createFromModelName($modelName) {
    $workflow = new self();
    return $workflow->setWorkflowModelName($modelName);
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
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param string $title
   * @return Workflow
   */
  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  /**
   * @return string
   */
  public function getState() {
    return $this->state;
  }

  /**
   * @return array
   */
  public function getAttachedDocumentIds() {
    $ids = [];
    foreach($this->attachedDocumentIds as $attachedDocumentId) {
      $ids[] = $attachedDocumentId['id'];
    }
    return $ids;
  }

  /**
   * @param array $attachedDocumentIds
   * @return Workflow
   */
  public function setAttachedDocumentIds($attachedDocumentIds) {
    $this->attachedDocumentIds = $attachedDocumentIds;
    return $this;
  }

  /**
   * @return string
   */
  public function getWorkflowModelName() {
    return $this->workflowModelName;
  }

  /**
   * @param string $workflowModelName
   * @return Workflow
   */
  public function setWorkflowModelName($workflowModelName) {
    $this->workflowModelName = $workflowModelName;
    return $this;
  }

  /**
   * @return string
   */
  public function getGraphResource() {
    return $this->graphResource;
  }

  /**
   * @param string $graphResource
   * @return Workflow
   */
  public function setGraphResource($graphResource) {
    $this->graphResource = $graphResource;
    return $this;
  }

  /**
   * @return void
   * @throws NuxeoClientException
   * @throws ClassCastException
   */
  public function cancel() {
    if($this->getNuxeoClient()) {
      $this->getNuxeoClient()->workflows()->cancelWorkflowById($this->getId());
      return;
    }
    throw new NuxeoClientException('You should pass to your Nuxeo object the client instance');
  }

}

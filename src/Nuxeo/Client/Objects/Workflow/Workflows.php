<?php


namespace Nuxeo\Client\Objects\Workflow;


use JMS\Serializer\Annotation as Serializer;
use Nuxeo\Client\Constants;
use Nuxeo\Client\Objects\AbstractEntityList;

class Workflows extends AbstractEntityList {

  /**
   * @var Workflow[]
   * @Serializer\Type("array<Nuxeo\Client\Objects\Workflow\Workflow>")
   */
  private $entries;

  /**
   * Workflows constructor.
   * @param \Nuxeo\Client\NuxeoClient $nuxeoClient
   */
  public function __construct($nuxeoClient) {
    parent::__construct(Constants::ENTITY_TYPE_WORKFLOWS, $nuxeoClient);
  }

  /**
   * @return Workflow[]
   */
  protected function &getEntries() {
    return $this->entries;
  }

}

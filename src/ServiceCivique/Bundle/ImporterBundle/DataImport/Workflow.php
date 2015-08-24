<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport;

use Ddeboer\DataImport\Workflow as BaseWorkflow;
use ServiceCivique\Bundle\ImporterBundle\DataImport\Writer\Writer;

/**
 * Class Workflow
 */
abstract class Workflow extends BaseWorkflow implements WorkflowInterface
{
    protected $entity_manager;

    /**
     * build
     *
     */
    public function build()
    {
        $this->addFilters();
        $this->addItemsConverters();
        $this->addValueConverters();
        $this->addWriters();

       return $this;
    }

    /**
     * addFilters
     *
     */
    protected function addFilters()
    {
    }

    /**
     * addItemsConverters
     *
     */
    protected function addItemsConverters()
    {
    }

    /**
     * addValueConverters
     *
     */
    protected function addValueConverters()
    {
    }

    /**
     * addWriters
     *
     */
    protected function addWriters()
    {
        $writer = new Writer($this->getEntityManager(), $this->getEntityClass(), 'originalId', $this->logger);

        $this->addWriter($writer);
    }

    /**
     * getEntityManager
     *
     */
    public function getEntityManager()
    {
        return $this->entity_manager;
    }

    /**
     * setEntityManager
     *
     * @param mixed $entity_manager
     */
    public function setEntityManager($entity_manager)
    {
        $this->entity_manager = $entity_manager;

        return $this;
    }

}

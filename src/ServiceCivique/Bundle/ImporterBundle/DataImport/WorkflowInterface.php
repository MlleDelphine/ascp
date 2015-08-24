<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport;

/**
 *
 */
interface WorkflowInterface
{
    /**
     * build
     *
     * @return Workflow
     */
    public function build();

    /**
     * getEntityManager
     *
     */
    public function getEntityManager();

}

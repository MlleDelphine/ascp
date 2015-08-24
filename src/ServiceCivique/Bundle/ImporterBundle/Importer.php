<?php

namespace ServiceCivique\Bundle\ImporterBundle;

/**
 * Class Importer
 */
class Importer
{
    protected $workflows;

    public function addWorkflow($workflow)
    {
        $this->workflows[] = $workflow;
    }

    /**
     * run the workflow queue
     *
     * @return Importer
     */
    public function run()
    {
        foreach ($workfows as $workflow) {
            $workflow->process();
        }

        return $this;
    }

    /**
     * getWorkflows
     *
     */
    public function getWorkflows()
    {
        return $this->workflows;
    }

    /**
     * setWorkflows
     *
     * @param mixed $workflows
     */
    public function setWorkflows($workflows)
    {
        $this->workflows = $workflows;

        return $this;
    }
}

<?php

namespace ServiceCivique\Bundle\ArchiveBundle\Model;

trait Archivable
{
    /**
     * @var bool $archived
     */
    protected $archived = false;

    /**
     * Get originalId.
     *
     * @return $archived.
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * Set archived.
     *
     * @param archived the value to set.
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * isArchived
     *
     */
    public function isArchived()
    {
        return (bool) $this->archived;
    }

    /**
     * isNotArchived
     *
     */
    public function isNotArchived()
    {
        return !$this->isArchived();
    }
}

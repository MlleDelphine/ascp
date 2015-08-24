<?php

namespace ServiceCivique\Bundle\ImporterBundle\Model;

trait Importable
{
    /**
     * @var string $originalId
     */
    protected $originalId;

    /**
     * Get originalId.
     *
     * @return originalId.
     */
    public function getOriginalId()
    {
        return $this->originalId;
    }

    /**
     * Set originalId.
     *
     * @param originalId the value to set.
     */
    public function setOriginalId($originalId)
    {
        $this->originalId = $originalId;

        return $this;
    }
}

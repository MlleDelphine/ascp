<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

/**
 * MissionLog
 */
class MissionLog
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $dataValue;

    /**
     * @var \ServiceCivique\Bundle\CoreBundle\Entity\Mission
     */
    private $mission;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dataValue
     *
     * @param  string     $dataValue
     * @return MissionLog
     */
    public function setDataValue($dataValue)
    {
        $this->dataValue = $dataValue;

        return $this;
    }

    /**
     * Get dataValue
     *
     * @return string
     */
    public function getDataValue()
    {
        return $this->dataValue;
    }

    /**
     * Set mission
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Mission $mission
     * @return MissionLog
     */
    public function setMission(\ServiceCivique\Bundle\CoreBundle\Entity\Mission $mission = null)
    {
        $this->mission = $mission;

        return $this;
    }

    /**
     * Get mission
     *
     * @return \ServiceCivique\Bundle\CoreBundle\Entity\Mission
     */
    public function getMission()
    {
        return $this->mission;
    }
}

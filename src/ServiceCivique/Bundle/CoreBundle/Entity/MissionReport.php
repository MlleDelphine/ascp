<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MissionReport
 */
class MissionReport
{

    const REPORT_DIPLOMA  = 1;
    const REPORT_TASK     = 2;
    const REPORT_JOB      = 3;
    const REPORT_INTEREST = 4;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \ServiceCivique\Bundle\CoreBundle\Entity\Mission
     */
    private $mission;

    /**
     * @var integer
     */
    private $countDiploma = 0;

    /**
     * @var integer
     */
    private $countTask = 0;

    /**
     * @var integer
     */
    private $countJob = 0;

    /**
     * @var integer
     */
    private $countInterest = 0;


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
     * Set countDiploma
     *
     * @param integer $countDiploma
     * @return MissionReport
     */
    public function setCountDiploma($countDiploma)
    {
        $this->countDiploma = $countDiploma;

        return $this;
    }

    /**
     * Get countDiploma
     *
     * @return integer
     */
    public function getCountDiploma()
    {
        return $this->countDiploma;
    }

    /**
     * Set countTask
     *
     * @param integer $countTask
     * @return MissionReport
     */
    public function setCountTask($countTask)
    {
        $this->countTask = $countTask;

        return $this;
    }

    /**
     * Get countTask
     *
     * @return integer
     */
    public function getCountTask()
    {
        return $this->countTask;
    }

    /**
     * Set countJob
     *
     * @param integer $countJob
     * @return MissionReport
     */
    public function setCountJob($countJob)
    {
        $this->countJob = $countJob;

        return $this;
    }

    /**
     * Get countJob
     *
     * @return integer
     */
    public function getCountJob()
    {
        return $this->countJob;
    }

    /**
     * Set countInterest
     *
     * @param integer $countInterest
     * @return MissionReport
     */
    public function setCountInterest($countInterest)
    {
        $this->countInterest = $countInterest;

        return $this;
    }

    /**
     * Get countInterest
     *
     * @return integer
     */
    public function getCountInterest()
    {
        return $this->countInterest;
    }

    /**
     * Set mission
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Mission $mission
     * @return MissionReport
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

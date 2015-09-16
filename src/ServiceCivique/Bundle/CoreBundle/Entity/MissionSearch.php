<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use ServiceCivique\Bundle\AddressingBundle\Model\LocationTrait;

class MissionSearch
{
    use LocationTrait;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $query;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $published;

    /**
     * @var integer
     */
    private $duration;

    /**
     * @var integer
     */
    private $weeklyWorkingHours;

    /**
     * @var boolean
     */
    private $isOverseas;

    /**
     * @var integer
     */
    private $vacancies;

    /**
     * @var \ServiceCivique\Bundle\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $taxons;

    /**
     * @var array
     */
    private $statuses = array(Mission::STATUS_AVAILABLE);

    /**
     * @var string
     */
    private $organization;

    /**
     * @var string
     */
    private $approvalNumber;

    /**
     * @var string
     */
    private $optionsTag;

    /**
     * @var integer
     */
    private $tag;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->taxons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Checks whether this is an advanced search
     *
     * @return (bool)
     */
    public function isAdvanced()
    {
        $statuses = $this->getStatuses();

        return (
            $this->getCountry() != null
            || $this->getDepartment() != null
            || $this->getQuery() != null
            || $this->getTaxons()->count() > 0
            || !empty($statuses)
        );
    }

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
     * Set query
     *
     * @param  string        $query
     * @return MissionSearch
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set startDate
     *
     * @param  \DateTime     $startDate
     * @return MissionSearch
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set duration
     *
     * @param  integer       $duration
     * @return MissionSearch
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set weeklyWorkingHours
     *
     * @param  integer       $weeklyWorkingHours
     * @return MissionSearch
     */
    public function setWeeklyWorkingHours($weeklyWorkingHours)
    {
        $this->weeklyWorkingHours = $weeklyWorkingHours;

        return $this;
    }

    /**
     * Get weeklyWorkingHours
     *
     * @return integer
     */
    public function getWeeklyWorkingHours()
    {
        return $this->weeklyWorkingHours;
    }

    /**
     * Set isOverseas
     *
     * @param  boolean       $isOverseas
     * @return MissionSearch
     */
    public function setIsOverseas($isOverseas)
    {
        $this->isOverseas = $isOverseas;

        return $this;
    }

    /**
     * Get isOverseas
     *
     * @return boolean
     */
    public function getIsOverseas()
    {
        return $this->isOverseas;
    }

    /**
     * Set vacancies
     *
     * @param  integer       $vacancies
     * @return MissionSearch
     */
    public function setVacancies($vacancies)
    {
        $this->vacancies = $vacancies;

        return $this;
    }

    /**
     * Get vacancies
     *
     * @return integer
     */
    public function getVacancies()
    {
        return $this->vacancies;
    }

    /**
     * Set user
     *
     * @param  \ServiceCivique\Bundle\UserBundle\Entity\User $user
     * @return MissionSearch
     */
    public function setUser(\ServiceCivique\Bundle\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \ServiceCivique\Bundle\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add taxons
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Taxon $taxons
     * @return MissionSearch
     */
    public function addTaxon(\ServiceCivique\Bundle\CoreBundle\Entity\Taxon $taxons)
    {
        $this->taxons[] = $taxons;

        return $this;
    }

    /**
     * Remove taxons
     *
     * @param \ServiceCivique\Bundle\CoreBundle\Entity\Taxon $taxons
     */
    public function removeTaxon(\ServiceCivique\Bundle\CoreBundle\Entity\Taxon $taxons)
    {
        $this->taxons->removeElement($taxons);
    }

    /**
     * Get taxons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxons()
    {
        return $this->taxons;
    }

    /**
     * Set statuses
     *
     * @param  array         $statuses
     * @return MissionSearch
     */
    public function setStatuses($statuses)
    {
        $this->statuses = $statuses;

        return $this;
    }

    /**
     * Get statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * Set organization
     *
     * @param  string        $organization
     * @return MissionSearch
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set approval number
     *
     * @param  string        $approvalNumber
     * @return MissionSearch
     */
    public function setApprovalNumber($approvalNumber)
    {
        $this->approvalNumber = $approvalNumber;

        return $this;
    }

    /**
     * Get approval number
     *
     * @return string
     */
    public function getApprovalNumber()
    {
        return $this->approvalNumber;
    }

    /**
     * Get approval number
     *
     * @return string
     */
    public function getOptionsTag()
    {
        return $this->optionsTag;
    }

    /**
     * Set approval number
     *
     * @param  string        $optionsTag
     * @return MissionSearch
     */
    public function setOptionsTag($optionsTag)
    {
        $this->optionsTag = $optionsTag;

        return $this;
    }

    /**
     * Get published.
     *
     * @return DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * setPublished
     *
     * @param DateTime $published
     */
    public function setPublished($published)
    {
        $this->published = ($published instanceof \DateTime) ? $published : null;
    }

    /**
     * Set tag
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Tag $tag
     * @return Application
     */
    public function setTag(\ServiceCivique\Bundle\CoreBundle\Entity\Tag $tag = null)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return \ServiceCivique\Bundle\CoreBundle\Entity\Tag
     */
    public function getTag()
    {
        return $this->tag;
    }
}

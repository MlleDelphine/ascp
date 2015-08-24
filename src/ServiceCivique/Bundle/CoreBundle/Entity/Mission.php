<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use ServiceCivique\Bundle\ImporterBundle\Model\Importable;
use ServiceCivique\Bundle\ArchiveBundle\Model\Archivable;
use ServiceCivique\Bundle\AddressingBundle\Model\LocationTrait;
use ServiceCivique\Bundle\CoreBundle\Traits\ApplicationStats;

/**
 * Mission
 */
class Mission
{
    use Archivable;
    use Importable;
    use LocationTrait;
    use ApplicationStats;

    const STATUS_DRAFT            = 0;
    const STATUS_AVAILABLE        = 1;
    const STATUS_FILLED           = 2;
    const STATUS_UNDER_REVIEW     = 3;
    const STATUS_UNDER_VALIDATION = 4;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var integer
     */
    private $duration;

    /**
     * @var integer
     */
    private $weeklyWorkingHours;

    /**
     * @var string
     */
    private $contact;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $website;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var \DateTime
     */
    private $published;

    /**
     * @var integer
     */
    private $status = self::STATUS_UNDER_REVIEW;

    /**
     * @var integer
     */
    private $previousStatus;

    /**
     * @var boolean
     */
    private $isOverseas;

    /**
     * @var integer
     */
    private $vacancies = 1;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $approvalNumber;

    /**
     * @var string
     */
    private $originalApprovalNumber;

    /**
     * @var string
     */
    private $organizationDescription;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $taxon;

    /**
     * @var \ServiceCivique\Bundle\CoreBundle\Entity\Organization
     */
    private $organization;

    /**
     * @var string
     */
    private $organizationName;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $applications;

    /**
     * @var string
     */
    private $applicationCount = 0;

    /**
     * @var \ServiceCivique\Bundle\CoreBundle\Entity\MissionLog
     */
    private $missionLog;

    /**
     * @var integer
     */
    private $duplicate;

    /**
     * @var integer
     */
    private $tag;

    /**
     * @var string
     */
    private $additionalEmailContact;

    /**
     * @var text
     */
    private $note;

    /**
     * @var text
     */
    private $comment;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->startDate = new \DateTime('+15 days');
    }

    /**
     * isAvailable
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->status == self::STATUS_AVAILABLE;
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
     * Set title
     *
     * @param  string  $title
     * @return Mission
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param  string  $description
     * @return Mission
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set startDate
     *
     * @param  \DateTime $startDate
     * @return Mission
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
     * @param  integer $duration
     * @return Mission
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
     * @param  integer $weeklyWorkingHours
     * @return Mission
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
     * Set contact
     *
     * @param  string  $contact
     * @return Mission
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set phoneNumber
     *
     * @param  string  $phoneNumber
     * @return Mission
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set website
     *
     * @param  string  $website
     * @return Mission
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set created
     *
     * @param  \DateTime $created
     * @return Mission
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param  \DateTime $updated
     * @return Mission
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set published
     *
     * @param  \DateTime $published
     * @return Mission
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return Mission
     */
    public function setStatus($status)
    {
        // store the old status
        $this->previousStatus = $this->status;

        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getPreviousStatus()
    {
        return $this->previousStatus;
    }

    /**
     * isStatusUpdated
     *
     * @return boolean
     */
    public function isStatusUpdated()
    {
        if ($this->previousStatus == self::STATUS_DRAFT) {
            return false;
        }

        // Special case duplicate with modifications => send mails
        if ($this->status == self::STATUS_UNDER_VALIDATION && $this->previousStatus == self::STATUS_UNDER_REVIEW) {
            return false;
        }

        return $this->status != $this->previousStatus;
    }

    public function isDraft()
    {
        return $this->status == self::STATUS_DRAFT;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isOverseas
     *
     * @param  boolean $isOverseas
     * @return Mission
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
     * @param  integer $vacancies
     * @return Mission
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
     * Set slug
     *
     * @param  string  $slug
     * @return Mission
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set approvalNumber
     *
     * @param  string  $approvalNumber
     * @return Mission
     */
    public function setApprovalNumber($approvalNumber)
    {
        $this->approvalNumber = $approvalNumber;

        return $this;
    }

    /**
     * Get approvalNumber
     *
     * @return string
     */
    public function getApprovalNumber()
    {
        return $this->approvalNumber;
    }

     /**
     * Set originalApprovalNumber
     *
     * @param  string       $originalApprovalNumber
     * @return Mission
     */
    public function setOriginalApprovalNumber($originalApprovalNumber)
    {
        $this->originalApprovalNumber = $originalApprovalNumber;

        return $this;
    }

    /**
     * Get originalApprovalNumber
     *
     * @return string
     */
    public function getOriginalApprovalNumber()
    {
        return $this->originalApprovalNumber;
    }

    /**
     * Set organizationDescription
     *
     * @param  string  $organizationDescription
     * @return Mission
     */
    public function setOrganizationDescription($organizationDescription)
    {
        $this->organizationDescription = $organizationDescription;

        return $this;
    }

    /**
     * Get organizationDescription
     *
     * @return string
     */
    public function getOrganizationDescription()
    {
        return $this->organizationDescription;
    }

    /**
     * Set organization
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Organization $organization
     * @return Mission
     */
    public function setOrganization(\ServiceCivique\Bundle\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \ServiceCivique\Bundle\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set taxon
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Taxon $taxon
     * @return Mission
     */
    public function setTaxon(\ServiceCivique\Bundle\CoreBundle\Entity\Taxon $taxon = null)
    {
        $this->taxon = $taxon;

        return $this;
    }

    /**
     * Get taxon
     *
     * @return \ServiceCivique\Bundle\CoreBundle\Entity\Taxon
     */
    public function getTaxon()
    {
        return $this->taxon;
    }

    /**
     * Set organizationName
     *
     * @param  string  $organizationName
     * @return Mission
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName = $organizationName;

        return $this;
    }

    /**
     * Get organizationName
     *
     * @return string
     */
    public function getOrganizationName()
    {
        if ($this->getFakeOrganizationName()) {
            return $this->getFakeOrganizationName();
        }

        if (!$this->getOrganization()) {
            return '';
        }

        return $this->getOrganization()->getName();
    }

    /**
     * getFakeOrganizationName
     *
     */
    public function getFakeOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * Add applications
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Application $applications
     * @return Mission
     */
    public function addApplication(\ServiceCivique\Bundle\CoreBundle\Entity\Application $applications)
    {
        $this->applications[] = $applications;

        return $this;
    }

    /**
     * Remove applications
     *
     * @param \ServiceCivique\Bundle\CoreBundle\Entity\Application $applications
     */
    public function removeApplication(\ServiceCivique\Bundle\CoreBundle\Entity\Application $applications)
    {
        $this->applications->removeElement($applications);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Set applicationCount
     *
     * @param  integer $applicationCount
     * @return Mission
     */
    public function setApplicationCount($applicationCount)
    {
        $this->applicationCount = $applicationCount;

        return $this;
    }


    /**
     * Set MissionLog
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\MissionLog $MissionLog
     * @return Mission
     */
    public function setMissionLog(\ServiceCivique\Bundle\CoreBundle\Entity\MissionLog $missionLog = null)
    {
        $this->missionLog = $missionLog;

        return $this;
    }

    /**
     * Get MissionLog
     *
     * @return \ServiceCivique\Bundle\CoreBundle\Entity\MissionLog
     */
    public function getMissionLog()
    {
        return $this->missionLog;
    }

    /**
     * Set duplicate
     *
     * @param  integer $duplicate
     * @return Mission
     */
    public function setDuplicate($duplicate)
    {
        $this->duplicate = $duplicate;

        return $this;
    }

    /**
     * Get duplicate
     *
     * @return string
     */
    public function getDuplicate()
    {
        return $this->duplicate;
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

    /**
     * Get additionalEmailContact
     *
     * @return string
     */
    public function getAdditionalEmailContact()
    {
        return $this->additionalEmailContact;
    }

    /**
     * Set additionalEmailContact
     *
     * @param string $additionalEmailContact
     */
    public function setAdditionalEmailContact($additionalEmailContact)
    {
        $this->additionalEmailContact = $additionalEmailContact;

        return $this;
    }

    /**
     * Get note
     *
     * @return text
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set note
     *
     * @param text $note
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get comment
     *
     * @return text
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }
}

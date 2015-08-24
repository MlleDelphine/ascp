<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use ServiceCivique\Bundle\ImporterBundle\Model\Importable;
use ServiceCivique\Bundle\ArchiveBundle\Model\Archivable;
use ServiceCivique\Bundle\AddressingBundle\Model\LocationTrait;
use Doctrine\Common\Collections\Criteria;

/**
 * Organization
 */
class Organization
{

    use Archivable;
    use Importable;
    use LocationTrait;

    const TYPE_APPROVED = 1;
    const TYPE_HOST     = 2;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

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
    private $previousApprovalNumber;

    /**
     * @var \DateTime
     */
    private $lastDecisionDate;

    /**
     * @var boolean
     */
    private $isApproved;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $website;

    /**
     * @var string
     */
    private $contactEmail;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $missions;

    /**
     * @var \ServiceCivique\Bundle\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var integer
     */
    private $type = self::TYPE_APPROVED;

    /**
     * @var \ServiceCivique\Bundle\CoreBundle\Entity\Organization
     */
    private $approvedOrganization;

    private $organizations;

    private $invitation;

    /**
     * @var boolean
     */
    private $todo;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * isApprovedOrganization
     * @return boolean
     */
    public function isApprovedOrganization()
    {
        return $this->type == self::TYPE_APPROVED;
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
     * Set name
     *
     * @param  string       $name
     * @return Organization
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param  string       $description
     * @return Organization
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
     * Set phoneNumber
     *
     * @param  string       $phoneNumber
     * @return Organization
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
     * @param  string       $website
     * @return Organization
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
     * Constructor
     */
    public function __construct()
    {
        $this->missions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set contactEmail
     *
     * @param  string       $contactEmail
     * @return Organization
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * Get contactEmail
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Add missions
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Mission $missions
     * @return Organization
     */
    public function addMission(\ServiceCivique\Bundle\CoreBundle\Entity\Mission $missions)
    {
        $this->missions[] = $missions;

        return $this;
    }

    /**
     * Remove missions
     *
     * @param \ServiceCivique\Bundle\CoreBundle\Entity\Mission $missions
     */
    public function removeMission(\ServiceCivique\Bundle\CoreBundle\Entity\Mission $missions)
    {
        $this->missions->removeElement($missions);
    }

    /**
     * Get missions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMissions()
    {
        return $this->missions;
    }

    /**
     * Set approvalNumber
     *
     * @param  string       $approvalNumber
     * @return Organization
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
     * @return Organization
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
     * Set previousApprovalNumber
     *
     * @param  string       $previousApprovalNumber
     * @return Organization
     */
    public function setPreviousApprovalNumber($previousApprovalNumber)
    {
        $this->previousApprovalNumber = $previousApprovalNumber;

        return $this;
    }

    /**
     * Get previousApprovalNumber
     *
     * @return string
     */
    public function getPreviousApprovalNumber()
    {
        return $this->previousApprovalNumber;
    }

    /**
     * Set lastDecisionDate
     *
     * @param  \Datetime       $lastDecisionDate
     * @return Organization
     */
    public function setLastDecisionDate($lastDecisionDate)
    {
        $this->lastDecisionDate = $lastDecisionDate;

        return $this;
    }

    /**
     * Get lastDecisionDate
     *
     * @return \Datetime
     */
    public function getLastDecisionDate()
    {
        return $this->lastDecisionDate;
    }

    /**
     * Set isApproved
     *
     * @param  boolean      $isApproved
     * @return Organization
     */
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * Get isApproved
     *
     * @return boolean
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * Set user
     *
     * @param  \ServiceCivique\Bundle\UserBundle\Entity\User $user
     * @return Organization
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
     * Set type
     *
     * @param  integer      $type
     * @return Organization
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set approvedOrganization
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Organization $approvedOrganization
     * @return Organization
     */
    public function setApprovedOrganization(\ServiceCivique\Bundle\CoreBundle\Entity\Organization $approvedOrganization = null)
    {
        $this->approvedOrganization = $approvedOrganization;

        return $this;
    }

    /**
     * Get approvedOrganization
     *
     * @return \ServiceCivique\Bundle\CoreBundle\Entity\Organization
     */
    public function getApprovedOrganization()
    {
        return $this->approvedOrganization;
    }

    /**
     * Add organizations
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Organization $organizations
     * @return Organization
     */
    public function addOrganization(\ServiceCivique\Bundle\CoreBundle\Entity\Organization $organizations)
    {
        $this->organizations[] = $organizations;

        return $this;
    }

    /**
     * Remove organizations
     *
     * @param \ServiceCivique\Bundle\CoreBundle\Entity\Organization $organizations
     */
    public function removeOrganization(\ServiceCivique\Bundle\CoreBundle\Entity\Organization $organizations)
    {
        $this->organizations->removeElement($organizations);
    }

    /**
     * Get organizations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }

    /**
     * Set invitation
     *
     * @return Organization
     */
    public function getInvitation()
    {
        return $this->invitation;
    }

    /**
     * Get invitation
     *
     * @param \ServiceCivique\Bundle\UserBundle\Entity\OrganizationInvitation $invitation
     */
    public function setInvitation(\ServiceCivique\Bundle\UserBundle\Entity\OrganizationInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * find an "chidren" organization matching $organizationName argument
     *
     * @param string $organizationName
     */
    public function findOrganization($organizationName)
    {
        if ($organizationName == $this->name) {
            return $this;
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("name", $organizationName))
        ;

        return $this->getOrganizations()->matching($criteria)->first();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set todo
     *
     * @param  boolean      $todo
     * @return Organization
     */
    public function setTodo($todo)
    {
        $this->todo = $todo;

        return $this;
    }

    /**
     * Get todo
     *
     * @return boolean
     */
    public function getTodo()
    {
        return $this->todo;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Organization
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     * @return Organization
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string 
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return Organization
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set area
     *
     * @param string $area
     * @return Organization
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string 
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Organization
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Organization
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Organization
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
     * @param \DateTime $updated
     * @return Organization
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
}

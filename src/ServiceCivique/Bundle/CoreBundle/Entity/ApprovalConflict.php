<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApprovalConflict
 */
class ApprovalConflict
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $approvalNumber;

    /**
     * @var integer
     */
    private $problemType;

    /**
     * @var string
     */
    private $organizationName;

    /**
     * @var string
     */
    private $contactEmail;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var integer
     */
    private $isResolved = 0;


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
     * Set approvalNumber
     *
     * @param string $approvalNumber
     * @return ApprovalConflict
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
     * Set problemType
     *
     * @param integer $problemType
     * @return ApprovalConflict
     */
    public function setProblemType($problemType)
    {
        $this->problemType = $problemType;

        return $this;
    }

    /**
     * Get problemType
     *
     * @return integer 
     */
    public function getProblemType()
    {
        return $this->problemType;
    }

    /**
     * Set organizationName
     *
     * @param string $organizationName
     * @return ApprovalConflict
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
        return $this->organizationName;
    }

    /**
     * Set contactEmail
     *
     * @param string $contactEmail
     * @return ApprovalConflict
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
     * Set firstname
     *
     * @param string $firstname
     * @return ApprovalConflict
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return ApprovalConflict
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return ApprovalConflict
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set isResolved
     *
     * @param integer $isResolved
     * @return ApprovalConflict
     */
    public function setIsResolved($isResolved)
    {
        $this->isResolved = $isResolved;

        return $this;
    }

    /**
     * Get isResolved
     *
     * @return integer
     */
    public function getIsResolved()
    {
        return $this->isResolved;
    }
}

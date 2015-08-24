<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

/**
 * Approval
 */
class Approval
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
     * @var string
     */
    private $organizationName;

    /**
     * @var \DateTime
     */
    private $decisionDate;

    /**
     * @var \DateTime
     */
    private $termDate;

    /**
     * @var integer
     */
    private $jobNumber;

    /**
     * @var text
     */
    private $missionLabels;

    /**
     * @var string
     */
    private $taxonomy;

    /**
     * @var string
     */
    private $referentAddress;

    /**
     * @var string
     */
    private $pdfUrl;

    /**
     * @var string
     */
    private $review;

    /**
     * @var string
     */
    private $oscarUrl;

    /**
     * @var string
     */
    private $siret;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $city;

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
     * @param  string   $approvalNumber
     * @return Approval
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
     * Set organizationName
     *
     * @param  string   $organizationName
     * @return Approval
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
     * Set decisionDate
     *
     * @param  \DateTime $decisionDate
     * @return Approval
     */
    public function setDecisionDate($decisionDate)
    {
        $this->decisionDate = ($decisionDate instanceof \DateTime) ? $decisionDate : null;

        return $this;
    }

    /**
     * Get decisionDate
     *
     * @return \DateTime
     */
    public function getDecisionDate()
    {
        return $this->decisionDate;
    }

    /**
     * Set termDate
     *
     * @param  \DateTime $termDate
     * @return Approval
     */
    public function setTermDate($termDate)
    {
        $this->termDate = ($termDate instanceof \DateTime) ? $termDate : null;

        return $this;
    }

    /**
     * Get termDate
     *
     * @return \DateTime
     */
    public function getTermDate()
    {
        return $this->termDate;
    }

    /**
     * Set jobNumber
     *
     * @param  integer  $jobNumber
     * @return Approval
     */
    public function setJobNumber($jobNumber)
    {
        $this->jobNumber = $jobNumber;

        return $this;
    }

    /**
     * Get jobNumber
     *
     * @return integer
     */
    public function getJobNumber()
    {
        return $this->jobNumber;
    }

    /**
     * Set missions
     *
     * @param  string   $missionLabels
     * @return Approval
     */
    public function setMissionLabels($missionLabels)
    {
        $this->missionLabels = $missionLabels;

        return $this;
    }

    /**
     * Get missionLabels
     *
     * @return string
     */
    public function getMissionLabels()
    {
        return $this->missionLabels;
    }

    /**
     * Set taxonomy
     *
     * @param  string   $taxonomy
     * @return Approval
     */
    public function setTaxonomy($taxonomy)
    {
        $this->taxonomy = $taxonomy;

        return $this;
    }

    /**
     * Get taxonomy
     *
     * @return string
     */
    public function getTaxonomy()
    {
        return $this->taxonomy;
    }

    /**
     * Set referentAddress
     *
     * @param  string   $referentAddress
     * @return Approval
     */
    public function setReferentAddress($referentAddress)
    {
        $this->referentAddress = $referentAddress;

        return $this;
    }

    /**
     * Get referentAddress
     *
     * @return string
     */
    public function getReferentAddress()
    {
        return $this->referentAddress;
    }

    /**
     * Set pdfUrl
     *
     * @param  string   $pdfUrl
     * @return Approval
     */
    public function setPdfUrl($pdfUrl)
    {
        $this->pdfUrl = $pdfUrl;

        return $this;
    }

    /**
     * Get pdfUrl
     *
     * @return string
     */
    public function getPdfUrl()
    {
        return $this->pdfUrl;
    }

    /**
     * Set review
     *
     * @param  string   $review
     * @return Approval
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }
    /**
     * Set oscarUrl
     *
     * @param  string   $oscarUrl
     * @return Approval
     */
    public function setOscarUrl($oscarUrl)
    {
        $this->oscarUrl = $oscarUrl;

        return $this;
    }

    /**
     * Get oscarUrl
     *
     * @return string
     */
    public function getOscarUrl()
    {
        return $this->oscarUrl;
    }

    /**
     * Set siret
     *
     * @param  string   $siret
     * @return Approval
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return string
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set address
     *
     * @param  string   $address
     * @return Approval
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
     * @param  string   $zipCode
     * @return Approval
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
     * Set city
     *
     * @param  string   $city
     * @return Approval
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
}

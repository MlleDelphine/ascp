<?php

namespace ServiceCivique\Bundle\UserBundle\Entity;

class OrganizationInvitation
{
    private $id;
    /**
     * @var boolean
     */
    private $sent;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \ServiceCivique\Bundle\CoreBundle\Entity\Organization
     */
    private $organization;

    /**
     * @var \DateTime
     */
    private $used_at;

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->code = substr(md5(uniqid(rand(), true)), 0, 12);
        $this->sent = false;
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
     * Set sent
     *
     * @param  boolean                $sent
     * @return OrganizationInvitation
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return boolean
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set code
     *
     * @param  string                 $code
     * @return OrganizationInvitation
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set email
     *
     * @param  string                 $email
     * @return OrganizationInvitation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set organization
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Organization $organization
     * @return OrganizationInvitation
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
     * Set used_at
     *
     * @param  \DateTime              $usedAt
     * @return OrganizationInvitation
     */
    public function setUsedAt($usedAt)
    {
        $this->used_at = $usedAt;

        return $this;
    }

    /**
     * Get used_at
     *
     * @return \DateTime
     */
    public function getUsedAt()
    {
        return $this->used_at;
    }
}

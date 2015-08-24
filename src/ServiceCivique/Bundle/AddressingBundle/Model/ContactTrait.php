<?php

namespace ServiceCivique\Bundle\AddressingBundle\Model;

trait ContactTrait
{
    /**
     * phoneNumber
     *
     * @var string
     */
    protected $phoneNumber;

    /**
     * contactName
     *
     * @var string
     */
    protected $contactName;

    /**
     * contactEmail
     *
     * @var string
     */
    protected $contactEmail;

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * setPhoneNumber
     *
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * getContactName
     *
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * setContactName
     *
     * @param string $contactName
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;
    }

    /**
     * getContactEmail
     *
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * setContactEmail
     *
     * @param string $contactEmail
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }
}

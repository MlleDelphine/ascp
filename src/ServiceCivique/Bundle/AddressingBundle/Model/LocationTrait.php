<?php
namespace ServiceCivique\Bundle\AddressingBundle\Model;

trait LocationTrait
{
    /**
     * @var string
     */
    protected $country = 'FR';

    /**
     * @var string
     */
    protected $area;

    /**
     * @var string
     */
    protected $department;

    /**
     * @var string
     */
    protected $zipCode;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $address;

    public function getCountry()
    {
        return $this->country;
    }

    /**
     * setCountry
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * getArea
     *
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * setArea
     *
     * @param string $area
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * getDepartment
     *
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * setDepartment
     *
     * @param string $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * getZipCode
     *
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * setZipCode
     *
     * @param string $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * getCity
     *
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * setCity
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * getAddress
     *
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * setAddress
     *
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }
}

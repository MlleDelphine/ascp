<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

/**
 * Header
 */
class Header
{
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
    private $pinImage;

    /**
     * @var string
     */
    private $pinUrl;

    /**
     * @var string
     */
    private $image;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

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
     * @param  string $title
     * @return Header
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
     * Set pinImage
     *
     * @param  string $pinImage
     * @return Header
     */
    public function setPinImage($pinImage)
    {
        $this->pinImage = $pinImage;

        return $this;
    }

    /**
     * Get pinImage
     *
     * @return string
     */
    public function getPinImage()
    {
        return $this->pinImage;
    }

    /**
     * Set pinUrl
     *
     * @param  string $pinUrl
     * @return Header
     */
    public function setPinUrl($pinUrl)
    {
        $this->pinUrl = $pinUrl;

        return $this;
    }

    /**
     * Get pinUrl
     *
     * @return string
     */
    public function getPinUrl()
    {
        return $this->pinUrl;
    }

    /**
     * Set image
     *
     * @param  string $image
     * @return Header
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set startDate
     *
     * @param  \DateTime $startDate
     * @return Header
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
     * Set endDate
     *
     * @param  \DateTime $endDate
     * @return Header
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}

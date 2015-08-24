<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

/**
 * Tag
 */
class Tag
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
    private $slug;

    /**
     * @var \ServiceCivique\Bundle\CoreBundle\Entity\Mission
     */
    private $missions;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Tag
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
     * Set slug
     *
     * @param  string $slug
     * @return Tag
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
     * Add missions
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Mission $missions
     * @return Mission
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
}

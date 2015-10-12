<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Forbidden Keyword.
 */
class ForbiddenKeyword
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $hits;
    
    /**
     * @var string
     */
    private $slug;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ForbiddenKeyword
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set hits.
     *
     * @param int $hits
     *
     * @return ForbiddenKeyword
     */
    public function setHits($hits)
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * Get Hits.
     *
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return ForbiddenKeyword
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add one hit
     *
     * @return int
     */
    public function addOneHit()
    {
        $hits = $this->getHits();
        $hits += 1;
        $this->setHits($hits);

        return $hits;
    }

}

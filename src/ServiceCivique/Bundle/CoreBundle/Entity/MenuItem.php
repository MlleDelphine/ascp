<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

/**
 * MenuItem
 */
class MenuItem
{

    const PRESENTATION                = 'presentation';
    const DEVENIR_VOLONTAIRE          = 'devenir_volontaire';
    const EN_MISSION                  = 'en_mission';
    const NOT_AGREED_ORGANIZATION     = 'not_agreed_organization';
    const AGREED_ORGANIZATION         = 'agreed_organization';
    const AGENCE_SERVICE_CIVIQUE      = 'agence_service_civique';
    const ESPACE_PRESSE_COMMUNICATION = 'espace_presse_communication';

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $parent;

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $position;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->position = 0;
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
     * Set parent
     *
     * @param  string   $parent
     * @return MenuItem
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set route
     *
     * @param  string   $route
     * @return MenuItem
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set title
     *
     * @param  string   $title
     * @return MenuItem
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
     * Set position
     *
     * @param  string   $position
     * @return MenuItem
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }
}

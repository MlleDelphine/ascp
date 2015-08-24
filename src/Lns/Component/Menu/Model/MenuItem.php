<?php

namespace Lns\Component\Menu\Model;

class MenuItem implements MenuItemInterface
{
    /**
     * id
     *
     * @var mixed
     */
    protected $id;

    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * url
     *
     * @var string
     */
    protected $url;

    /**
     * parent
     *
     * @var MenuItemInterface
     */
    protected $parent;

    /**
     * children
     *
     * @var mixed
     */
    protected $children;

    /**
     * weight
     *
     * @var mixed
     */
    protected $weight = 0;

    protected $options = array();

    /**
     * @var integer
     */
    private $root;

    /**
     * @var integer
     */
    private $level;

    /**
     * @var integer
     */
    private $left;

    /**
     * @var integer
     */
    private $right;

    /**
     * menu
     *
     * @var MenuInterface
     */
    protected $menu;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return MenuItemInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param  MenuItemInterface $parent
     * @return MenuItemInterface
     */
    public function setParent(MenuItemInterface $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function getOptions()
    {
        $options = $this->options ? $this->options : [];

        return array_merge($options, [
            'uri' => $this->url
        ]);
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param  string            $name
     * @return MenuItemInterface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get menu
     *
     * @return MenuInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set menu
     *
     * @param  MenuInterface     $menu
     * @return MenuItemInterface
     */
    public function setMenu(MenuInterface $menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Add children
     *
     * @param  MenuItemInterface $child
     * @return MenuItemInterface
     */
    public function addChild(MenuItemInterface $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove children
     *
     * @param MenuItemInterface $applications
     */
    public function removeChild(MenuItemInterface $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get id.
     *
     * @return id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param id the value to set.
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getIndentation($pattern = "&nbsp;", $repeat = 10)
    {
        return str_repeat($pattern, $this->level * $repeat);
    }

    public function __toString()
    {
        return $this->getIndentation() . $this->name;
    }

    /**
     * Get level.
     *
     * @return level.
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Get left.
     *
     * @return left.
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Get weight.
     *
     * @return weight.
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set weight.
     *
     * @param weight the value to set.
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get url.
     *
     * @return url.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url.
     *
     * @param url the value to set.
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}

<?php

namespace Lns\Component\Menu\Model;

class Menu implements MenuInterface
{
    protected $id;
    protected $title;
    protected $items;
    protected $rootMenuItem;
    protected $slug;
    protected $updated;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get title.
     *
     * @return title.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param title the value to set.
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * getRootMenuItem
     *
     * @return MenuItemInterface
     */
    public function getRootMenuItem()
    {
        return $this->rootMenuItem;
    }

    /**
     * setRootMenuItem
     *
     * @param  MenuItemInterface $rootMenuItem
     * @return MenuInterface     $menu
     */
    public function setRootMenuItem(MenuItemInterface $rootMenuItem)
    {
        $this->rootMenuItem = $rootMenuItem;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return slug.
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug.
     *
     * @param slug the value to set.
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * Get items.
     *
     * @return items.
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set items.
     *
     * @param items the value to set.
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Add items
     *
     * @param  MenuItemInterface $item
     * @return MenuInterface
     */
    public function addItem(MenuItemInterface $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return updated.
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated.
     *
     * @param updated the value to set.
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }
}

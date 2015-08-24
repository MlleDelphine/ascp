<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use Eko\FeedBundle\Item\Writer\RoutedItemInterface;

/**
 * Presse
 */
class Presse implements RoutedItemInterface
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
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $text;

    /**
     * @var \DateTime
     */
    private $published;

    /**
     * @var string
     */
    private $slug;

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
     * @return Presse
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
     * Set type
     *
     * @param  integer $type
     * @return Presse
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set text
     *
     * @param  string $text
     * @return Presse
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set published
     *
     * @param  \DateTime $published
     * @return Presse
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set slug
     *
     * @param  string $slug
     * @return Presse
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

    public function getFeedItemTitle() {
        return $this->getTitle();
    }
    public function getFeedItemDescription() {
        return $this->getText();
    }
    public function getFeedItemPubDate() {
        return $this->getPublished();
    }

    public function getFeedItemRouteName() {
        return 'service_civique_presse_show';
    }

    public function getFeedItemRouteParameters() {
        return array(
            'slug' => $this->getSlug()
        );
    }

    public function getFeedItemUrlAnchor() {
        return;
    }
}

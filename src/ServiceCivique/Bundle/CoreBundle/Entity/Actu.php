<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use Eko\FeedBundle\Item\Writer\RoutedItemInterface;

/**
 * Actu
 */
class Actu implements RoutedItemInterface
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
    private $text;

    /**
     * @var string
     */
    private $description;

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
     * @return Actu
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
     * Set text
     *
     * @param  string $text
     * @return Actu
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
     * Set description
     *
     * @param  string $description
     * @return Actu
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \DateTime
     */
    private $published;

    /**
     * Set slug
     *
     * @param  string $slug
     * @return Actu
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
     * Set published
     *
     * @param  \DateTime $published
     * @return Actu
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

    public function getFeedItemTitle() {
        return $this->getTitle();
    }
    public function getFeedItemDescription() {
        return $this->getDescription();
    }
    public function getFeedItemPubDate() {
        return $this->getPublished();
    }

    public function getFeedItemRouteName() {
        return 'service_civique_actu_show';
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

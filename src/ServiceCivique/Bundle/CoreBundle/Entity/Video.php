<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use Eko\FeedBundle\Item\Writer\RoutedItemInterface;

class Video implements RoutedItemInterface
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
    private $description;

    /**
     * @var string
     */
    private $transcription;

    /**
     * @var string
     */
    private $url;

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
     * @return Video
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
     * Set url
     *
     * @param  string $url
     * @return Video
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set published
     *
     * @param  \DateTime $published
     * @return Video
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
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param  string $slug
     * @return Video
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param  string $description
     * @return Video
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get transcription
     *
     * @return string $transcription
     */
    public function getTranscription()
    {
        return $this->transcription;
    }

    /**
     * Set transcription
     *
     * @param  string $transcription
     * @return Video
     */
    public function setTranscription($transcription)
    {
        $this->transcription = $transcription;

        return $this;
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
        return 'service_civique_video_show';
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

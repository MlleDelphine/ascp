<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Eko\FeedBundle\Item\Writer\RoutedItemInterface;

/**
 * Major Program.
 */
class MajorProgram implements RoutedItemInterface
{
    const ICON_INSTITUTION_FLAG = 1;
    const ICON_TREE = 2;
    const ICON_BOOK = 3;
    const ICON_MASKS = 4;
    const ICON_HEALTH = 5;
    const ICON_BRIEFCASE = 6;
    const ICON_BUILDINGS = 7;
    const ICON_DIPLOMA = 8;
    const ICON_SPORTS = 9;
    const ICON_INSTITUTION_LEAF = 10;
    const ICON_CAP = 11;

    public static function getIcons()
    {
        return [
            self::ICON_INSTITUTION_FLAG => 'Bâtiment administratif',
            self::ICON_TREE             => 'Arbre',
            self::ICON_BOOK             => 'Livre',
            self::ICON_MASKS            => 'Masques',
            self::ICON_HEALTH           => 'Santé',
            self::ICON_BRIEFCASE        => 'Malette',
            self::ICON_BUILDINGS        => 'Immeubles',
            self::ICON_DIPLOMA          => 'Diplômes',
            self::ICON_SPORTS           => 'Sports',
            self::ICON_INSTITUTION_LEAF => 'Feuille',
            self::ICON_CAP              => 'Képi',
        ];
    }

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $icon;

    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $position;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \DateTime
     */
    private $published;


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
     * Set title.
     *
     * @param string $title
     *
     * @return MajorProgram
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set URL.
     *
     * @param int $url
     *
     * @return MajorProgram
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get URL.
     *
     * @return int
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set position.
     *
     * @param int $position
     *
     * @return MajorProgram
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set icon.
     *
     * @param int $icon
     *
     * @return MajorProgram
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon.
     *
     * @return int
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return MajorProgram
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
     * Set published.
     *
     * @param \DateTime $published
     *
     * @return MajorProgram
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published.
     *
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    public function getFeedItemTitle()
    {
        return $this->getTitle();
    }

    public function getFeedItemDescription()
    {
        return $this->getDescription();
    }

    public function getFeedItemPubDate()
    {
        return $this->getPublished();
    }

    public function getFeedItemRouteName()
    {
        return 'service_civique_major_program_show';
    }

    public function getFeedItemRouteParameters()
    {
        return array(
            'slug' => $this->getSlug(),
        );
    }

    public function getFeedItemUrlAnchor()
    {
        return;
    }
}

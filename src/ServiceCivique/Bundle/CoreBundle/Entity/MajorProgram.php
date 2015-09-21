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
    private $path;

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

    private $file;
    private $temp;

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
     * Set path.
     *
     * @param string $path
     *
     * @return MajorProgram
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/major_programs';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename   = sha1(uniqid(mt_rand(), true));
            $this->path = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->path);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
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

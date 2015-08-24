<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use ServiceCivique\Bundle\ImporterBundle\Model\Importable;
use ServiceCivique\Bundle\AddressingBundle\Model\LocationTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ServiceCivique\Bundle\CoreBundle\Model\ResumableInterface;

class Profile implements ResumableInterface
{
    use Importable;
    use LocationTrait;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $gender;

    /**
     * @var integer
     */
    private $educationLevel;

    /**
     * @var \DateTime
     */
    private $birthDate;

    /**
     * @var string
     */
    private $motivation;

    /**
     * @var boolean
     */
    private $isPublic = 0;

    /**
     * @var boolean
     */
    private $receiveInformations = 0;

    /**
     * @var boolean
     */
    private $AAH = 0;

    /**
     * @var boolean
     */
    private $RQTH = 0;

    /**
     * @var \ServiceCivique\Bundle\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $path;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var string
     */
    private $cv;

    private $file;
    private $temp;

    /**
     * @var integer
     */
    private $hasProfileVisited = 0;

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
     * Set gender
     *
     * @param  integer $gender
     * @return Profile
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return integer
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set educationLevel
     *
     * @param  integer $educationLevel
     * @return Profile
     */
    public function setEducationLevel($educationLevel)
    {
        $this->educationLevel = $educationLevel;

        return $this;
    }

    /**
     * Get educationLevel
     *
     * @return integer
     */
    public function getEducationLevel()
    {
        return $this->educationLevel;
    }

    /**
     * Set birthDate
     *
     * @param  \DateTime $birthDate
     * @return Profile
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set motivation
     *
     * @param  string  $motivation
     * @return Profile
     */
    public function setMotivation($motivation)
    {
        $this->motivation = $motivation;

        return $this;
    }

    /**
     * Get motivation
     *
     * @return string
     */
    public function getMotivation()
    {
        return $this->motivation;
    }

    /**
     * Set cv
     *
     * @param  string  $cv
     * @return Profile
     */
    public function setCv($cv)
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * Get cv
     *
     * @return string
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * Set isPublic
     *
     * @param  boolean $isPublic
     * @return Profile
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic
     *
     * @return boolean
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set receiveInformations
     *
     * @param  boolean $receiveInformations
     * @return Profile
     */
    public function setReceiveInformations($receiveInformations)
    {
        $this->receiveInformations = $receiveInformations;

        return $this;
    }

    /**
     * Get receiveInformations
     *
     * @return boolean
     */
    public function getReceiveInformations()
    {
        return $this->receiveInformations;
    }

    /**
     * Set AAH
     *
     * @param  boolean $aAH
     * @return Profile
     */
    public function setAAH($aAH)
    {
        $this->AAH = $aAH;

        return $this;
    }

    /**
     * Get AAH
     *
     * @return boolean
     */
    public function getAAH()
    {
        return $this->AAH;
    }

    /**
     * Set RQTH
     *
     * @param  boolean $rQTH
     * @return Profile
     */
    public function setRQTH($rQTH)
    {
        $this->RQTH = $rQTH;

        return $this;
    }

    /**
     * Get RQTH
     *
     * @return boolean
     */
    public function getRQTH()
    {
        return $this->RQTH;
    }

    /**
     * Set user
     *
     * @param  \ServiceCivique\Bundle\UserBundle\Entity\User $user
     * @return Profile
     */
    public function setUser(\ServiceCivique\Bundle\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \ServiceCivique\Bundle\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set phoneNumber
     *
     * @param  string  $phoneNumber
     * @return Profile
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    protected function generateResumeUrl()
    {
        $extension = $this->getFile()->guessExtension();

        $name = $this->getUser()->getFullName();

        if (trim($name) == '') {
            $name = $this->getUser()->getUsername();
        }

        $name = \Gedmo\Sluggable\Util\Urlizer::urlize($name, '-');

        return sprintf('%s-%s', substr(sha1($this->getUser()->getId() . time()), 0, 6), $name) . '.' .$extension;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/cv';
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
        if (is_file($this->getAbsolutePath())) {
            // store the old name to delete after the update
            $this->temp = $this->getAbsolutePath();
        } else {
            $this->path = 'initial';
        }
    }

    public function preUpload()
    {
        $this->updated = new \DateTime();
        if (null !== $this->getFile()) {
            $this->path = $this->generateResumeUrl();
        }
    }

    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            // unlink($this->temp);
            // clear the temp image path
            $this->temp = null;
        }

        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->path
        );

        $this->setFile(null);
    }

    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    public function removeUpload()
    {
        if (isset($this->temp)) {
            //unlink($this->temp);
        }
    }

    // Force doctrine tracking if we only change our resume
    public function postLoad()
    {
        $this->updated = new \DateTime();
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

    /**
     * Set path
     *
     * @param  string  $path
     * @return Profile
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set hasProfileVisited
     *
     * @param  integer  $hasProfileVisited
     * @return Profile
     */
    public function setHasProfileVisited($hasProfileVisited)
    {
        $this->hasProfileVisited = $hasProfileVisited;

        return $this;
    }

    /**
     * Get hasProfileVisited
     *
     * @return integer
     */
    public function getHasProfileVisited()
    {
        return $this->hasProfileVisited;
    }
}

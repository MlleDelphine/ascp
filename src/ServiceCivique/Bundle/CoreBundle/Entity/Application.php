<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use ServiceCivique\Bundle\CoreBundle\Model\ResumableInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Application implements ResumableInterface
{
    const WAITING_ANSWER  = 0;
    const POSITIVE_ANSWER = 1;
    const NEGATIVE_ANSWER = 2;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     * is provided ?
     */
    private $missionStatus;

    /**
     * @var string
     */
    private $messageSubject;

    /**
     * @var string
     */
    private $messageText;

    /**
     * @var \DateTime
     */
    private $messageDate;

    /**
     * @var \ServiceCivique\Bundle\CoreBundle\Entity\Mission
     */
    private $mission;

    /**
     * @var \ServiceCivique\Bundle\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var string
     */
    private $path;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var \DateTime
     */
    private $isPoked = null;

    /**
     * @var string
     */
    private $cv;

    private $file;
    private $temp;

    /**
     * @var integer
     */
    private $isPreview = 1;

    /**
     * @var integer
     */
    private $isSelected = 0;

    public function copyApplicationPathToUserProfilePath()
    {
        // if user is new copy application path in user profile path
        if (!$this->getUser()->getId()) {
            $this->getUser()->getProfile()->setPath($this->getPath());
        }
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
     * isAnswered
     * @return boolean
     */
    public function isAnswered()
    {
        return !$this->status == self::WAITING_ANSWER;
    }

    /**
     * Set created
     *
     * @param  \DateTime   $created
     * @return Application
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set status
     *
     * @param  integer     $status
     * @return Application
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set missionStatus
     *
     * @param  integer     $missionStatus
     * @return Application
     */
    public function setMissionStatus($missionStatus)
    {
        $this->missionStatus = $missionStatus;

        return $this;
    }

    /**
     * Get missionStatus
     *
     * @return integer
     */
    public function getMissionStatus()
    {
        return $this->missionStatus;
    }

    /**
     * Set messageSubject
     *
     * @param  string      $messageSubject
     * @return Application
     */
    public function setMessageSubject($messageSubject)
    {
        $this->messageSubject = $messageSubject;

        return $this;
    }

    /**
     * Get messageSubject
     *
     * @return string
     */
    public function getMessageSubject()
    {
        return $this->messageSubject;
    }

    /**
     * Set messageText
     *
     * @param  string      $messageText
     * @return Application
     */
    public function setMessageText($messageText)
    {
        $this->messageText = $messageText;

        return $this;
    }

    /**
     * Get messageText
     *
     * @return string
     */
    public function getMessageText()
    {
        return $this->messageText;
    }

    /**
     * Set messageDate
     *
     * @param  \DateTime   $messageDate
     * @return Application
     */
    public function setMessageDate($messageDate)
    {
        $this->messageDate = $messageDate;

        return $this;
    }

    /**
     * Get messageDate
     *
     * @return \DateTime
     */
    public function getMessageDate()
    {
        return $this->messageDate;
    }

    /**
     * Set mission
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Mission $mission
     * @return Application
     */
    public function setMission(\ServiceCivique\Bundle\CoreBundle\Entity\Mission $mission = null)
    {
        $this->mission = $mission;

        return $this;
    }

    /**
     * Get mission
     *
     * @return \ServiceCivique\Bundle\CoreBundle\Entity\Mission
     */
    public function getMission()
    {
        return $this->mission;
    }

    /**
     * Set user
     *
     * @param  \ServiceCivique\Bundle\UserBundle\Entity\User $user
     * @return Application
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
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $motivation;

    /**
     * Set zipCode
     *
     * @param  string      $zipCode
     * @return Application
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set city
     *
     * @param  string      $city
     * @return Application
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set phoneNumber
     *
     * @param  string      $phoneNumber
     * @return Application
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

    /**
     * Set address
     *
     * @param  string      $address
     * @return Application
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set motivation
     *
     * @param  string      $motivation
     * @return Application
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
     * @var string
     */
    private $country;

    /**
     * Set country
     *
     * @param  string      $country
     * @return Application
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
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

    protected function generateResumeUrl()
    {
        $extension = $this->getFile()->guessExtension();

        $name = $this->getUser()->getFullName();

        if (trim($name) == '') {
            $name = $this->getUser()->getUsername();
        }

        $name = \Gedmo\Sluggable\Util\Urlizer::urlize($name, '-');

        return sprintf('%s-%s', substr(sha1($this->getUser()->getId() . time()), 0, 6), $name) . '.' . $extension;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
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
     * @param  string      $path
     * @return Application
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
     * Set isPoked
     *
     * @param  datetime    $isPoked
     * @return Application
     */
    public function setIsPoked($isPoked)
    {
        $this->isPoked = $isPoked;

        return $this;
    }

    /**
     * Get isPoked
     *
     * @return datetime
     */
    public function getIsPoked()
    {
        return $this->isPoked;
    }

    /**
     * Set isPreview
     *
     * @param  integer     $isPreview
     * @return Application
     */
    public function setIsPreview($isPreview)
    {
        $this->isPreview = $isPreview;

        return $this;
    }

    /**
     * Get isPreview
     *
     * @return integer
     */
    public function getIsPreview()
    {
        return $this->isPreview;
    }

    /**
     * Set isSelected
     *
     * @param  integer     $isSelected
     * @return Application
     */
    public function setIsSelected($isSelected)
    {
        $this->isSelected = $isSelected;

        return $this;
    }

    /**
     * Get isSelected
     *
     * @return integer
     */
    public function getIsSelected()
    {
        return $this->isSelected;
    }
}

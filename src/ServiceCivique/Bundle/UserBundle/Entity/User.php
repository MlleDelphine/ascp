<?php

namespace ServiceCivique\Bundle\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use ServiceCivique\Bundle\ImporterBundle\Model\Importable;
use ServiceCivique\Bundle\ArchiveBundle\Model\Archivable;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;
use ServiceCivique\Bundle\CoreBundle\Traits\ApplicationStats;

use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;

/**
 * User
 */
class User extends BaseUser implements EncoderAwareInterface
{
    use Archivable;
    use Importable;
    use ApplicationStats;

    const MISSION_SEEKER_TYPE   = 1;
    const ORGANIZATION_TYPE     = 2;
    const VOLUNTEER_TYPE        = 3;
    const FORMER_VOLUNTEER_TYPE = 4;

    public function isJeune()
    {
        return in_array($this->type, array(
            self::MISSION_SEEKER_TYPE,
            self::VOLUNTEER_TYPE,
            self::FORMER_VOLUNTEER_TYPE
        ));
    }

    public function isOrganization()
    {
        return $this->type == self::ORGANIZATION_TYPE;
    }

    public function __construct()
    {
        $this->applications = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct();
        // your own logic
    }


    /**
     * getFullName
     *
     */
    public function getFullName()
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $applications;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \ServiceCivique\Bundle\CoreBundle\Entity\Profile
     */
    private $profile;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var boolean
     */
    private $isNewsletterSubscribed = false;

    /**
     * @var \ServiceCivique\Bundle\CoreBundle\Entity\Organization
     */
    private $organization;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var integer
     */
    private $subscriptionReferer;

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
     * Set profile
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Profile $profile
     * @return User
     */
    public function setProfile(\ServiceCivique\Bundle\CoreBundle\Entity\Profile $profile = null)
    {
        $profile->setUser($this);
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \ServiceCivique\Bundle\CoreBundle\Entity\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set type
     *
     * @param  integer $type
     * @return User
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
     * {@inheritDoc}
     */
    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);
    }

    /**
     * Set isNewsletterSubscribed
     *
     * @param  boolean $isNewsletterSubscribed
     * @return Profile
     */
    public function setIsNewsletterSubscribed($isNewsletterSubscribed)
    {
        $this->isNewsletterSubscribed = $isNewsletterSubscribed;

        return $this;
    }

    /**
     * Get isNewsletterSubscribed
     *
     * @return boolean
     */
    public function getIsNewsletterSubscribed()
    {
        return $this->isNewsletterSubscribed;
    }

    /**
     * Set organization
     *
     * @param  \ServiceCivique\Bundle\CoreBundle\Entity\Organization $organization
     * @return User
     */
    public function setOrganization(\ServiceCivique\Bundle\CoreBundle\Entity\Organization $organization = null)
    {
        $organization->setUser($this);
        $organization->setContactEmail($this->getEmail());
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \ServiceCivique\Bundle\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set created
     *
     * @param  \DateTime $created
     * @return Mission
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
     * Set updated
     *
     * @param  \DateTime $updated
     * @return Mission
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set lastname
     *
     * @param  string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param  string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set subscriptionReferer
     *
     * @param  integer $subscriptionReferer
     * @return User
     */
    public function setSubscriptionReferer($subscriptionReferer)
    {
        $this->subscriptionReferer = $subscriptionReferer;

        return $this;
    }

    /**
     * Get subscriptionReferer
     *
     * @return integer
     */
    public function getSubscriptionReferer()
    {
        return $this->subscriptionReferer;
    }

    /**
     * Get encoderName
     *
     * @return string
     */
    public function getEncoderName()
    {
        if ($this->isLegacy()) {
            return 'legacy_encoder';
        }

        return null;
    }

    /**
     * isLegacy
     * @return bool
     */
    public function isLegacy()
    {
        return $this->originalId != null;
    }

    public function equals($user)
    {
        if (!method_exists($user, 'getId')) {
            return false;
        }

        return $this->getId() == $user->getId();
    }

    public function __toString()
    {
        return sprintf('%s (%s)', $this->getFullName(), $this->getEmail());
    }

    /**
     * Add applications
     *
     * @param  Application $applications
     * @return Mission
     */
    public function addApplication(Application $applications)
    {
        $this->applications[] = $applications;

        return $this;
    }

    /**
     * Remove applications
     *
     * @param Application $applications
     */
    public function removeApplication(Application $applications)
    {
        $this->applications->removeElement($applications);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }
}

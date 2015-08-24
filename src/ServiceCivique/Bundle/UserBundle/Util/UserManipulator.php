<?php

namespace ServiceCivique\Bundle\UserBundle\Util;

use FOS\UserBundle\Model\UserManagerInterface;

use ServiceCivique\Bundle\CoreBundle\Entity\Profile;

class UserManipulator
{
    private $userManager;

    /**
     * __construct
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Creates a user and returns it.
     *
     * @param string  $username
     * @param string  $password
     * @param string  $email
     * @param Boolean $active
     * @param Boolean $superadmin
     * @param string  $type
     * @param array   $datas
     *
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function create($username, $password, $email, $active, $superadmin, $type = null, $datas = array())
    {
        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled((Boolean) $active);
        $user->setSuperAdmin((Boolean) $superadmin);
        $user->setType($type);

        if ($user->isJeune()) {
            $user->addRole('ROLE_JEUNE');

            $profile = new Profile();
            $profile->setGender(isset($datas['gender']) ? $datas['gender'] : null);
            $profile->setBirthDate(
                \DateTime::createFromFormat(
                    'U',
                    rand(strtotime('1990-01-01'), strtotime('1999-01-01'))
                )
            );

            $user->setProfile($profile);
        }

        foreach ($datas as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (!method_exists($user, $method)) {
                continue;
            }
            $user->{$method}($value);
        }

        $this->userManager->updateUser($user);

        return $user;
    }

}

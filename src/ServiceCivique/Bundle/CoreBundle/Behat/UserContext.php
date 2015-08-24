<?php

namespace ServiceCivique\Bundle\CoreBundle\Behat;

use Behat\Gherkin\Node\TableNode;

use ServiceCivique\Bundle\UserBundle\Entity\User;

class UserContext extends DefaultContext
{

    /**
     * @Given /^I am logged in as administrator$/
     */
    public function iAmLoggedInAsAdministrator()
    {
        $this->iAmLoggedInAsRole('ROLE_ADMIN');
    }

    /**
     * @Given /^I am logged in user$/
     * @Given /^I am logged in as user "([^""]*)"$/
     */
    public function iAmLoggedInUser($email = 'service-civique@example.com')
    {
        $this->iAmLoggedInAsRole('ROLE_USER', $email);
    }

    /**
     * @Given /^I am logged in as "([^""]*)" organization user$/
     */
    public function iAmLoggedInAsOrganizationUser($organizationName, $email = 'service-civique@example.com')
    {
        $this->iAmLoggedInAsRole('ROLE_ORGANIZATION', $email, $organizationName);
    }

    /**
     * @Given /^there are following users:$/
     */
    public function thereAreFollowingUsers(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsUser(
                $data['email'],
                $data['password'],
                'ROLE_USER',
                isset($data['organization']) ? $data['organization'] : null,
                isset($data['enabled']) ? $data['enabled'] : true,
                isset($data['address']) && !empty($data['address']) ? $data['address'] : null,
                isset($data['groups']) && !empty($data['groups']) ? explode(',', $data['groups']) : array(),
                false
            );
        }

        $this->getEntityManager()->flush();
    }

    public function thereIsUser($email, $password, $role = null, $organization = null, $enabled = 'yes', $address = null, $groups = array(), $flush = true)
    {
        if (null === $user = $this->getRepository('user')->findOneBy(array('email' => $email))) {

            /* @var $user UserInterface */
            $user = $this->getRepository('user')->createNew();
            $user->setEmail($email);
            $user->setEnabled('yes' === $enabled);
            $user->setPlainPassword($password);

            if (null !== $role) {
                $user->addRole($role);
            }

            if (null !== $organization && $organizationEntity = $this->getRepository('organization')->findOneBy(array('name' => $organization))) {
                $user->setOrganization($organizationEntity);
                $user->addRole('ROLE_ORGANIZATION');
                $user->setType(User::ORGANIZATION_TYPE);
            }

            $this->getEntityManager()->persist($user);

            foreach ($groups as $groupName) {
                if ($group = $this->findOneByName('group', $groupName)) {
                    $user->addGroup($group);
                }
            }

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        return $user;
    }

    /**
     * Create user and login with given role.
     *
     * @param string $role
     * @param string $email
     */
    private function iAmLoggedInAsRole($role, $email = 'service-civique@example.com', $organization = null)
    {
        $this->thereIsUser($email, 'service-civique', $role, $organization);
        $this->getSession()->visit($this->generatePageUrl('fos_user_security_login'));

        $this->fillField('username', $email);
        $this->fillField('password', 'service-civique');
        $this->pressButton('_submit');
    }
}

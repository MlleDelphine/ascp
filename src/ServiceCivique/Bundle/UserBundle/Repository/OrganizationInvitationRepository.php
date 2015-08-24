<?php

namespace ServiceCivique\Bundle\UserBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use ServiceCivique\Bundle\CoreBundle\Entity\Organization;

class OrganizationInvitationRepository extends EntityRepository
{
    /**
     * create new invitation
     *
     * @param Organization $organization
     * @param string       $email
     */
    public function createNewFromOrganizationAndEmail($organization, $email)
    {
        $invitation = $this->createNew();
        $invitation->setOrganization($organization);
        $invitation->setEmail($email);

        return $invitation;
    }
}

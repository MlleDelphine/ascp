<?php

namespace ServiceCivique\Bundle\CoreBundle\Security\Authorization\Voter;

use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use ServiceCivique\Bundle\UserBundle\Entity\User;

class OwnerVoter
{
    public function supportsAttribute($attribute)
    {
        return 'OWNER' === $attribute;
    }

    public function supportsClass($class)
    {
        return $class instanceof Mission;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($this->supportsAttribute($attribute) && $this->supportsClass($object)) {
                $user = $token->getUser();

                // if user is not an instance of ServiceCiviqueUserBundle:User
                // then return VoterInterface::ACCESS_DENIED
                if (!$user instanceof User) {
                    return VoterInterface::ACCESS_DENIED;
                }

                $userOrganization = $user->getOrganization();

                // if connected user is not linked to an organization
                // then return ACCESS_DENIED
                if (!$userOrganization) {
                    return VoterInterface::ACCESS_DENIED;
                }

                $objectOrganization = $object->getOrganization();

                // if connected user organization has the same id as object organization
                // then return ACCESS_GRANTED
                if ($objectOrganization->getId() == $userOrganization->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                // try to find a sub organization which can match object organization
                foreach ($userOrganization->getOrganizations() as $childOrganization) {
                    if ($childOrganization->getId() != $objectOrganization->getId()) {
                        continue;
                    }

                    return VoterInterface::ACCESS_GRANTED;
                }
            }
        }

        return VoterInterface::ACCESS_DENIED;
    }
}

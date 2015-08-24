<?php

namespace ServiceCivique\Bundle\CoreBundle\Validator;

use Symfony\Component\Validator\ExecutionContextInterface;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;

class OrganizationValidator
{
    /**
     * Validate than approved organization isset if organization type is TYPE_HOST
     *
     * @param mixed                     $organization
     * @param ExecutionContextInterface $context
     */
    public static function validateApprovedOrganization($organization, ExecutionContextInterface $context)
    {
        if ($organization->isApprovedOrganization()) {
            return;
        }

        if (!$organization->getApprovedOrganization()) {
            $context->addViolationAt(
                'approvedOrganization',
                'un organisme d’accueil doit être rattaché à un organisme agréé',
                array(),
                null
            );
        }
    }
}

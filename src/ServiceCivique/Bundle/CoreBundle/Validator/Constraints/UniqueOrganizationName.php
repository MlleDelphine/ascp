<?php

namespace ServiceCivique\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueOrganizationName extends Constraint
{
    public $message = 'Le nom d‘organisme "%string%" est déjà utilisé';

    public function validatedBy()
    {
        return 'unique_organization_name_validator';
    }
}

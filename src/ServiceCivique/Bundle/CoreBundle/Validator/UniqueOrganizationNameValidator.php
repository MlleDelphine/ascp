<?php

namespace ServiceCivique\Bundle\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\NonUniqueResultException;

class UniqueOrganizationNameValidator extends ConstraintValidator
{
    /**
     * @param ObjectRepository $organizationRepository
     */
    public function __construct(ObjectRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        try {
            $existingOrganization = $this->organizationRepository->findOneBy(array(
                'name' => $value
            ));
        } catch (NonUniqueResultException $e) {
            $existingOrganization = true;
        }

        if ($existingOrganization) {
            $this->context->addViolation($constraint->message, array('%string%' => $value));
        }
    }
}

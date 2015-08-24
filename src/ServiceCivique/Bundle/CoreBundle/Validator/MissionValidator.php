<?php

namespace ServiceCivique\Bundle\CoreBundle\Validator;

use Symfony\Component\Validator\ExecutionContextInterface;

class MissionValidator
{
    /**
     * validate than mission start date must be greater than today date +2 weeks
     *
     * @param mixed                     $object
     * @param ExecutionContextInterface $context
     */
    public static function validateNewMissionStartDate($object, ExecutionContextInterface $context)
    {
        if ($object->getId() != null) {
            return;
        }

        $startDate = $object->getStartDate();

        if (!$startDate || !$startDate instanceof \DateTime) {
            return false;
        }

        $now = new \DateTime();
        $interval = $now->diff($startDate);

        if ($interval->days < 14 || $interval->invert == 1) {
            $context->addViolationAt(
                'start_date',
                'La date de début de mission doit être supérieure à 2 semaines après la date du jour pour tenir compte du délai de validation.',
                array(),
                null
            );
        }
    }
}

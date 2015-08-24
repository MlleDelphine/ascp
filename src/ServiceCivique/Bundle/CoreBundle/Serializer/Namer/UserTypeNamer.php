<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Namer;

use ServiceCivique\Bundle\UserBundle\Entity\User;

class UserTypeNamer extends AbstractNumericValueNamer
{
    public function getMap() {
        return array(
            0                           => 'Indéterminé',
            User::MISSION_SEEKER_TYPE   => 'En recherche',
            User::ORGANIZATION_TYPE     => 'Organisme',
            User::VOLUNTEER_TYPE        => 'Volontaire',
            User::FORMER_VOLUNTEER_TYPE => 'Ancien volontaire'
        );
    }
}

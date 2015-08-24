<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Namer;

use ServiceCivique\Bundle\CoreBundle\Entity\Mission;

class MissionStatusNamer extends AbstractNumericValueNamer
{
    public function getMap() {
        return array(
            Mission::STATUS_DRAFT            => 'Brouillon',
            Mission::STATUS_UNDER_REVIEW     => 'En attente',
            Mission::STATUS_AVAILABLE        => 'À pourvoir',
            Mission::STATUS_FILLED           => 'Pourvue',
            Mission::STATUS_UNDER_VALIDATION => 'Modification en attente de validation'
        );
    }
}

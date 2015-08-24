<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Namer;

class UserGenderNamer extends AbstractNumericValueNamer
{
    public function getMap() {
        return array(
            0 => 'Homme',
            1 => 'Femme',
        );
    }
}

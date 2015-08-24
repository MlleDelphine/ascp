<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Namer;

use ServiceCivique\Bundle\CoreBundle\Entity\Application;

class ApplicationStatusNamer extends AbstractNumericValueNamer
{
    public function getMap() {
        return array(
            Application::WAITING_ANSWER  => 'En attente de réponse',
            Application::POSITIVE_ANSWER => 'Réponse positive',
            Application::NEGATIVE_ANSWER => 'Réponse négative'
        );
    }
}

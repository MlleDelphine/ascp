<?php

namespace ServiceCivique\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;

class ElasticaApplicationUpdater extends ElasticaDocumentUpdater
{
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof Mission) {
            $applications = $entity->getApplications();
            foreach($applications as $application) {
                $this->update($application);
            }
        }
    }
}

<?php

namespace ServiceCivique\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;

class ElasticaMissionUpdater extends ElasticaDocumentUpdater
{
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->handleApplicationPersistOrUpdate($eventArgs);
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $this->handleApplicationPersistOrUpdate($eventArgs);
    }

    protected function handleApplicationPersistOrUpdate(LifecycleEventArgs $eventArgs) {
        $entity = $eventArgs->getObject();

        // update mission on candidature persist or update
        if ($entity instanceof Application) {
            $mission = $entity->getMission();
            $this->update($mission);
        }
    }
}

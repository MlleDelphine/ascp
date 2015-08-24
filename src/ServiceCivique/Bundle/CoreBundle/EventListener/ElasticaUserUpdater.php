<?php

namespace ServiceCivique\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;

class ElasticaUserUpdater extends ElasticaDocumentUpdater
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

        // update user on candidature persist or update
        if ($entity instanceof Application) {
            $user = $entity->getUser();
            $this->update($user);
        }
    }
}

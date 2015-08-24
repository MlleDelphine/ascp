<?php

namespace ServiceCivique\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use FOS\ElasticaBundle\Persister\ObjectPersister;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\ElasticaBundle\Doctrine\Listener as BaseListener;

abstract class ElasticaDocumentUpdater extends BaseListener
{
    protected $container;
    protected $objectPersisterSession;

    public function setContainer(ContainerInterface $container, ObjectPersister $objectPersisterSession)
    {
        $this->container              = $container;
        $this->objectPersisterSession = $objectPersisterSession;
    }

    protected function update($object) {
        if ($this->objectPersister->handlesObject($object)) {
            $this->scheduledForUpdate[] = $object;
        }
    }

    public function postUpdate(LifecycleEventArgs $eventArgs) {
    }

    public function postPersist(LifecycleEventArgs $eventArgs) {
    }
}

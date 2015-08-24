<?php

namespace ServiceCivique\Bundle\ArchiveBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class ArchivableMapper implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        $reflexionClass = $metadata->getReflectionClass();

        if (!$reflexionClass) {
            return;
        }

        $traits = array_keys($reflexionClass->getTraits());

        if (!in_array('ServiceCivique\Bundle\ArchiveBundle\Model\Archivable', $traits)) {
            return;
        }

        $metadata->mapField(array(
            'fieldName'  => 'archived',
            'columnName' => 'archived',
            'nullable'   => false,
            'default'    => false,
            'type'       => 'boolean'
        ));

        $builder = new ClassMetadataBuilder($metadata);
        $builder->addIndex(array('archived'), 'archived_index');

    }
}

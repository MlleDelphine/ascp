<?php

namespace ServiceCivique\Bundle\ImporterBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class ImportableMapper implements EventSubscriber
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

        if (!in_array('ServiceCivique\Bundle\ImporterBundle\Model\Importable', $traits)) {
            return;
        }

        $metadata->mapField(array(
            'fieldName'  => 'originalId',
            'columnName' => 'original_id',
            'nullable'   => true,
            'type'       => 'integer'
        ));

        $builder = new ClassMetadataBuilder($metadata);
        $builder->addIndex(array('original_id'), 'original_id_index');

    }
}

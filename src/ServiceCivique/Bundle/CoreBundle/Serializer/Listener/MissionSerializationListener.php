<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;

class MissionSerializationListener implements EventSubscriberInterface
{
    private $missionStatusNamer;

    public function __construct($missionStatusNamer)
    {
        $this->missionStatusNamer = $missionStatusNamer;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event'  => 'serializer.post_serialize',
                'class'  => 'ServiceCivique\Bundle\CoreBundle\Entity\Mission',
                'method' => 'onPostSerialize'
            )
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        $visitor = $event->getVisitor();

        if ($object->getTaxon()) {
            $visitor->addData('taxon_name', $object->getTaxon()->getName());
            $visitor->addData('taxon_id', $object->getTaxon()->getId());
        }

        if ($object->getStatus() != null) {
            $visitor->addData('status_name', $this->missionStatusNamer->getName($object->getStatus()));
        }

        if($event->getContext()->getDepth() < 1) {
            $application_count = $object->getApplicationCount();
            $visitor->addData('application_count', $application_count);
        }
    }
}

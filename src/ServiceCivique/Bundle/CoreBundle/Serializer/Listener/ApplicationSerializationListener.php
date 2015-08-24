<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;

class ApplicationSerializationListener implements EventSubscriberInterface
{
    public function __construct($applicationStatusNamer, $missionStatusNamer)
    {
        $this->missionStatusNamer     = $missionStatusNamer;
        $this->applicationStatusNamer = $applicationStatusNamer;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event'  => 'serializer.post_serialize',
                'class'  => 'ServiceCivique\Bundle\CoreBundle\Entity\Application',
                'method' => 'onPostSerializeApplication'
            ),
        );
    }

    public function onPostSerializeApplication(ObjectEvent $event)
    {
        $object = $event->getObject();
        $visitor = $event->getVisitor();

        if($mission = $object->getMission()) {
            $visitor->addData('mission_id', $mission->getId());
            $visitor->addData('mission_status', $mission->getStatus());
            $visitor->addData('mission_status_name', $this->missionStatusNamer->getName($mission->getStatus()));
        }

        if($user = $object->getUser()) {
            $visitor->addData('user_id', $user->getId());
        }

        if ($object->getStatus() !== null) {
            $visitor->addData('status_name', $this->applicationStatusNamer->getName($object->getStatus()));
        }
    }
}

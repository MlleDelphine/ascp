<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

class ProfileSerializationListener implements EventSubscriberInterface
{
    private $userGenderNamer;

    public function __construct($userGenderNamer)
    {
        $this->userGenderNamer = $userGenderNamer;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event'  => 'serializer.post_serialize',
                'class'  => 'ServiceCivique\Bundle\CoreBundle\Entity\Profile',
                'method' => 'onPostSerialize'
            ),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        $visitor = $event->getVisitor();

        if($object->getBirthDate() !== null) {
            $visitor->addData('age', date('Y') - $object->getBirthDate()->format('Y'));
        }

        if ($object->getGender() !== null) {
            $visitor->addData('gender_name', $this->userGenderNamer->getName($object->getGender()));
        }
    }
}

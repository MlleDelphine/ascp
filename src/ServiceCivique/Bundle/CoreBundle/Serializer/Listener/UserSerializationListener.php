<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

class UserSerializationListener implements EventSubscriberInterface
{
    private $userRepository;
    private $userTypeNamer;

    public function __construct($userRepository, $userTypeNamer)
    {
        $this->userRepository   = $userRepository;
        $this->userTypeNamer   = $userTypeNamer;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event'  => 'serializer.post_serialize',
                'class'  => 'ServiceCivique\Bundle\UserBundle\Entity\User',
                'method' => 'onPostSerialize'
            ),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        $visitor = $event->getVisitor();

        if ($object->getType() !== null) {
            $visitor->addData('type_name', $this->userTypeNamer->getName($object->getType()));
        }

        if($event->getContext()->getDepth() < 1) {
            $application_count = $this->userRepository->getApplicationCount($object);

            $accepted_application_count = $this->userRepository->getAcceptedApplicationCount($object);
            $rejected_application_count = $this->userRepository->getRejectedApplicationCount($object);
            $pending_application_count = $this->userRepository->getPendingApplicationCount($object);

            $visitor->addData('application_count', $application_count);

            $visitor->addData('accepted_application_count', $accepted_application_count);
            $visitor->addData('rejected_application_count', $rejected_application_count);
            $visitor->addData('pending_application_count', $pending_application_count);

            if($application_count > 0) {
                $visitor->addData('rejected_application_rate', $rejected_application_count / $application_count);
                $visitor->addData('accepted_application_rate', $accepted_application_count / $application_count);
                $visitor->addData('pending_application_rate', $pending_application_count / $application_count);
            }
        }
    }
}

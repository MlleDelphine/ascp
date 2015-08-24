<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;

class OrganizationSerializationListener implements EventSubscriberInterface
{
    private $organizationRepository;

    public function __construct($organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event'  => 'serializer.post_serialize',
                'class'  => 'ServiceCivique\Bundle\CoreBundle\Entity\Organization',
                'method' => 'onPostSerialize'
            ),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        $visitor = $event->getVisitor();

        $visitor->addData('type_name', $this->getTypeName($object));

        if($event->getContext()->getDepth() < 1) {
            $application_count = $this->organizationRepository->getApplicationCount($object);

            $accepted_application_count = $this->organizationRepository->getAcceptedApplicationCount($object);
            $rejected_application_count = $this->organizationRepository->getRejectedApplicationCount($object);
            $pending_application_count = $this->organizationRepository->getPendingApplicationCount($object);

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

    protected function getTypeName($organization) {

        return $organization->isApprovedOrganization() ? "Organisme agréé" : "Organisme d’accueil";
    }

}

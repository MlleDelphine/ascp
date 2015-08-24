<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Persistence\ObjectRepository;

class SelectOrCreateNewOrganizationSubscriber implements EventSubscriberInterface
{
    /**
     * __construct
     *
     * @param ObjectRepository $organizationRepository
     */
    public function __construct(ObjectRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => array('preSubmit', 900)
        );
    }

    /**
     * preSubmit
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // if ($data['is_new_organization']) {

        // }
    }
}

<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;

class CreateNewOrganizationSubscriber implements EventSubscriberInterface
{
    /**
     * @param mixed $context
     */
    public function __construct($context)
    {
        $this->context                = $context;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::POST_SUBMIT   => 'postSubmit',
        );
    }

    /**
     * if current user organization is a approvedOrganization
     * then add organization field
     *
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $mission = $event->getData();

        $userOrganization = $this->getCurrentUserOrganization();

        if ($userOrganization && $userOrganization->isApprovedOrganization()) {
            $form->add('organization', 'select_or_create_new_organization', array(
                'label'             => false,
                'mission'           => $mission,
                'user_organization' => $userOrganization
            ));
            $form->add('approved_choice', 'choice', array(
                'mapped'   => false,
                'label'    => 'Je veux:',
                'required' => false,
                'empty_value' => false,
                'expanded' => true,
                'choices'  => array(
                    0 => 'Créer la mission pour ma structure',
                    1 => 'Déléguer la mission'
                )
            ));
        }
    }

    /**
     * after submit set organizationName to null
     *
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $mission = $event->getData();
        $mission->setOrganizationName(null);
        $event->setData($mission);
    }

    /**
     * getCurrentUserOrganization
     *
     */
    protected function getCurrentUserOrganization()
    {
        // retreive current organization user
        $user = $this->context->getToken()->getUser();

        $organization = null;

        if ($user instanceof User) {
            $organization = $user->getOrganization();
        }

        return $organization;
    }
}

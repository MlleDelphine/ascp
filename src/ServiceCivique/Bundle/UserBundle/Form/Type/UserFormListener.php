<?php

namespace ServiceCivique\Bundle\UserBundle\Form\Type;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use ServiceCivique\Bundle\UserBundle\Entity\User;

class UserFormListener implements EventSubscriberInterface
{
    protected $initialEmailFieldOptions;
    protected $lightmode;

    public function __construct(array $initialEmailFieldOptions = array(), $lightmode = false )
    {
        $this->initialEmailFieldOptions = $initialEmailFieldOptions;
        $this->lightmode = $lightmode;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData'
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();
        if (!$user) {
            return;
        }

        if ($user->getType() == User::ORGANIZATION_TYPE) {

            $emailFieldOptions['attr'] = array(
                'data-title' => "Cette adresse sera utilisée pour vous informer de l'évolution de vos missions et des candidatures associées. Veillez donc à la consulter régulièrement."
            );

            $this->alterEmailField($form, $this->initialEmailFieldOptions, $emailFieldOptions);

            /**
             * Added by F. Zilbermann 11/09/2015
             *
             * For use in backend form
             */
            $form->add('enabled', null, array(
                'label'     => 'Activation',
                'required'  => false
            ));

            $form->add('lastname', null, array(
                'label'     => 'Nom',
                'required'  => false
            ));

            $form->add('firstname', null, array(
                'label'     => 'Prénom',
                'required'  => false
            ));

            $form->add('organization', 'service_civique_organization', array(
                'error_bubbling'     => false,
                'property_path'      => 'organization',
                'validation_groups'  => array('ServiceCiviqueRegistration'),
                'cascade_validation' => false
            ));
        }

        if (in_array($user->getType(), array(
            User::MISSION_SEEKER_TYPE,
            User::VOLUNTEER_TYPE,
            User::FORMER_VOLUNTEER_TYPE
        ))) {

            $emailFieldOptions['attr'] = array(
                'data-title' => "Vous serez contacté par les organismes à cette adresse mail."
            );

            $this->alterEmailField($form, $this->initialEmailFieldOptions, $emailFieldOptions);

            /**
             * Added by F. Zilbermann 11/09/2015
             *
             * For use in backend form
             */
            $form->add('enabled', null, array(
                'label'     => 'Activation',
                'required'  => false
            ));

            $form->add('lastname', null, array(
                'label'     => 'Nom',
                'required'  => true
            ));

            $form->add('firstname', null, array(
                'label'     => 'Prénom',
                'required'  => true
            ));

            $form->add('profile', 'service_civique_profile', array(
                'error_bubbling'     => false,
                'property_path'      => 'profile',
                'validation_groups'  => array('ServiceCiviqueRegistration'),
                'cascade_validation' => false
            ));

            $form->add('type', 'choice', array(
                'attr' => array(
                    'class'          => 'show-tooltip',
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => "N'oubliez pas de changer votre statut lorsque vous êtes en mission afin de ne plus recevoir les alertes mails."
                ),
                'choices'   => array(
                    User::MISSION_SEEKER_TYPE   => 'En recherche de mission',
                    User::VOLUNTEER_TYPE        => 'Volontaire',
                    User::FORMER_VOLUNTEER_TYPE => 'Ancien volontaire',
                )
            ));

            if ($this->lightmode) {
                $form->get('profile')->remove('location');
                $form->get('profile')->remove('motivation');
                $form->get('profile')->remove('educationLevel');
                $form->get('profile')->remove('file');
                $form->remove('plainPassword');
                $form->remove('type');
            }
        }
        $form->remove('current_password');
    }

    protected function alterEmailField($form, $options, $moreOptions = array())
    {
        $default_options = array(
            'attr' => array(
                'class'          => 'show-tooltip',
                'data-placement' => 'top',
                'data-toggle'    => 'tooltip',
                'data-html'      => true,
                'data-trigger'   => 'focus',
            )
        );

        $options = array_merge_recursive($default_options, $options, $moreOptions);

        $form->remove('email');
        $form->add('email', 'email', $options);
    }
}

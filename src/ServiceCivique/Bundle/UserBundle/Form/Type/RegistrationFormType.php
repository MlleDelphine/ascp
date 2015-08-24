<?php

namespace ServiceCivique\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends BaseType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $emailFieldOptions = $builder->get('email')->getOptions();

        $builder->remove('username');

        $builder->add('isNewsletterSubscribed', 'checkbox', array(
            'label'     => 'Recevez les informations du Service Civique',
            'required'  => false
        ));
        $builder->add('subscriptionReferer', 'choice', array(
            'label'     => 'Comment avez-vous entendu parler du Service Civique ?',
            'choices'   => array(
                0 => 'par quelqu\'un de votre famille',
                1 => 'par un(e) ami(e)',
                2 => 'par la presse',
                3 => 'par un reportage télé',
                4 => 'par les bannières sur le web',
                5 => 'autre',
            ),
        ));

        // application registration all-in-one form
        $light_mode = isset($options['light_mode']) ? $options['light_mode'] : false;

        $builder->addEventSubscriber(new UserFormListener($emailFieldOptions, $light_mode));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'light_mode'         => false
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_user_registration';
    }
}

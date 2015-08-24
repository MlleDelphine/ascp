<?php

namespace ServiceCivique\Bundle\ContactBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;

class ContactType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_type', 'choice', array(
                'label'    => 'Votre demande concerne',
                'choices'   => array(
                    1 => 'Un jeune',
                    2 => 'Un organisme',
                    //0 => 'Autre',
                ),
                'expanded' => true,
                'required' => true,
                'attr' => array('class' => 'faq-select-choice')
            ))
            ->add('faq_level_1', 'choice', array(
                'label'    => false,
                'empty_value' => 'Choisissez une option',
                'attr'        => array('class' => 'faq-select faq-select-1'),
                'empty_data'  => null,
                'required'    => false,
            ))
            ->add('faq_level_2', 'choice', array(
                'label'    => false,
                'empty_value' => 'Choisissez une option',
                'attr'        => array('class' => 'faq-select faq-select-2'),
                'empty_data'  => null,
                'required'    => false,
            ))
            ->add('faq_level_3', 'choice', array(
                'label'    => false,
                'empty_value' => 'Choisissez une option',
                'attr'        => array('class' => 'faq-select faq-select-3'),
                'empty_data'  => null,
                'required'    => false,
            ))
            ->add('user_email', 'text', array(
                'label'    => 'Email',
                'required' => true,
            ))
            ->add('content', 'textarea', array(
                'label'    => 'Message',
                'required' => true,
            ))
        ;

        $builder->add('actions', 'form_actions');

        $builder->add('save', 'submit', array(
            'label' => 'Envoyer',
            'attr' => array('class' => 'btn btn-sc-red')
        ));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
            $event->stopPropagation();
        }, 900); // Always set a higher priority than ValidationListener
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // $resolver->setDefaults(array(
        //     'validation_groups' => false,
        // ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_contact';
    }
}

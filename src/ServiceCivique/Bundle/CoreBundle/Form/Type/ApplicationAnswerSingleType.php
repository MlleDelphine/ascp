<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;

class ApplicationAnswerSingleType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', 'choice', array(
                'label' => false,
                'choices'   => array(
                    Application::POSITIVE_ANSWER => 'Réponse positive',
                    Application::NEGATIVE_ANSWER => 'Réponse négative',
                ),
                'expanded' => true,
            ))
            ->add('messageText', null, array('label' => 'Message'))
        ;

        $builder->add('actions', 'form_actions');

        $builder->add('save', 'submit', array(
            'label' => 'Envoyer',
            'attr'  => array('class' => 'btn btn-sc-red'),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'ServiceCivique\Bundle\CoreBundle\Entity\Application',
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_application_answer_single';
    }
}

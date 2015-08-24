<?php

namespace ServiceCivique\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StaticContentType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content', 'textarea', array(
                'attr' => array(
                    'class' => 'wysiwyg',
                )
            ))
        ;

        $builder->add('actions', 'form_actions');

        $builder->add('save', 'submit', array(
            'label' => 'Valider',
            'attr' => array('class' => 'btn btn-sc-red')
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'ServiceCivique\Bundle\ContentBundle\Entity\StaticContent',
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_static_content';
    }
}

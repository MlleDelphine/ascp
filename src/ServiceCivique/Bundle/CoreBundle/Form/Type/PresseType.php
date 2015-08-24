<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PresseType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'Titre'))
            ->add('text', 'textarea', array(
                'label' => 'Contenu',
                'attr' => array(
                    'class' => 'wysiwyg',
                )
            ))
            ->add('type', 'choice', array(
                'label' => 'Type',
                'choices'   => array(
                    '1' => 'Communiqué',
                    '2' => 'Dossier',
                ),
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
            'data_class'         => 'ServiceCivique\Bundle\CoreBundle\Entity\Presse',
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_presse';
    }
}

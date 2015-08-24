<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PartnerType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'Titre'))
            ->add('subtitle', 'text', array('label' => 'Sous-titre'))
            ->add('text', 'textarea', array(
                'label' => 'Contenu',
                'attr' => array(
                    'class' => 'wysiwyg',
                )
            ))
            ->add('type', 'choice', array(
                'label' => 'Type',
                'choices'   => array(
                    '1' => 'Valoriser le Service Civique auprès des employeurs et impliquer les entreprises',
                    '2' => 'Encourager et promouvoir le Service Civique au niveau local',
                    '3' => 'Favoriser l’accueil des jeunes volontaires',
                    '4' => 'Favoriser le développement du Service Civique à l\'international',
                ),
            ))
            ->add('description', 'text', array('label' => 'Meta description'))
            ->add('file', 'file', array('label' => 'Fichier', 'required' => true))
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
            'data_class'         => 'ServiceCivique\Bundle\CoreBundle\Entity\Partner',
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_partner';
    }
}

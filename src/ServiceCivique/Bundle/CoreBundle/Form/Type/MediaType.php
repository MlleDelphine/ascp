<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ServiceCivique\Bundle\CoreBundle\Entity\Media;

class MediaType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'Titre'))
            ->add('type', 'choice', array(
                'label' => 'Type',
                'choices'   => array(
                    '1' => 'Presse',
                    '2' => 'Radio',
                    '3' => 'Télé',
                ),
            ))
            ->add('media_name', 'text', array('label' => 'Média'))
            ->add('published', 'date', array('label' => 'Date de publication'))
            ->add('url', 'text', array('label' => 'Lien', 'required' => false))
            ->add('file', 'file', array('label' => 'Fichier', 'required' => false))
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
            'data_class'         => 'ServiceCivique\Bundle\CoreBundle\Entity\Media',
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_media';
    }
}

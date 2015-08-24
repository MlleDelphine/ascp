<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ServiceCivique\Bundle\CoreBundle\Entity\MenuItem;

class MenuItemType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', 'choice', array(
                'choices' => array(
                    'Jeunes' => array(
                        MenuItem::PRESENTATION       => 'Présentation',
                        MenuItem::DEVENIR_VOLONTAIRE => 'Devenir volontaire',
                        MenuItem::EN_MISSION         => 'En mission',
                    ),
                    'Organisations' => array(
                        MenuItem::NOT_AGREED_ORGANIZATION => 'Vous n\'êtes pas encore un organisme agréé',
                        MenuItem::AGREED_ORGANIZATION     => 'Vous êtes un organisme agréé',
                    ),
                    'Corporate' => array(
                        MenuItem::AGENCE_SERVICE_CIVIQUE      => 'L\'Agence du Service Civique',
                        MenuItem::ESPACE_PRESSE_COMMUNICATION => 'Espace presse et communication',
                    ),
                ),
            ))
            ->add('title')
            ->add('route', null, array(
                'attr' => array('class' => 'typeahead')
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
            'data_class'         => 'ServiceCivique\Bundle\CoreBundle\Entity\MenuItem',
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_menu_item';
    }
}

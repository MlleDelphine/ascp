<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MissionSearchOptionsType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paginate', 'choice', array(
                'label'     => 'service_civique.form.mission_search_options.paginate.label',
                'choices'   => array(
                    12 => '12 missions par page',
                    24 => '24 missions par page',
                    36 => '36 missions par page'
                ),
                'required'    => false,
                'multiple'    => false,
                'empty_value' => false,
            ))
            ->add('sorting', 'choice', array(
                'label'     => 'service_civique.form.mission_search_options.order.label',
                'choices'   => array(
                    'start_date' => 'Date de début',
                    'published'  => 'Date de publication'
                ),
                'required'    => false,
                'multiple'    => false,
                'empty_value' => false,
            ))
            ->add('submit', 'submit', array('attr' => array('class' => 'btn btn-sc-red sr-only')));

    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        //$resolver->setDefaults();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_mission_search_options';
    }
}

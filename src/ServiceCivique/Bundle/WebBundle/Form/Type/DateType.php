<?php

namespace ServiceCivique\Bundle\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use ServiceCivique\Bundle\WebBundle\Form\DataTransformer\DateStringFormatFixer;

class DateType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new DateStringFormatFixer('d/m/Y', 'Y-m-d'));
    }

    /**
     * {@inheritDoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $date = $form->getData();
        $view->vars['formatted'] = ($date instanceof \DateTime) ? $date->format('d/m/Y') : $date;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_date';
    }

    public function getParent()
    {
        return 'date';
    }
}

<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class ApprovalNumberType extends AbstractType
{

    /**
     * Pass the basic field attributes to the view
     *
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $options['attr']['pattern']     = '^[A-Z]{2}-[0-9]{3}-[0-9]{2}-[0-9]{5}(?:-[0-9]{2}|$)$';
        $options['attr']['placeholder'] = 'XX-000-00-00000-00';
        $options['attr']['class']       = 'show-tooltip';
        $options['attr']['maxlength']   = 18;

        $view->vars = array_replace($view->vars, array(
            'attr' => $options['attr']
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'service_civique_approval_number';
    }
}

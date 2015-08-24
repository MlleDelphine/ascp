<?php

namespace ServiceCivique\Bundle\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ImportApprovalFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attachment', 'file')
            ->add('save', 'submit', array(
                'label' => 'Valider',
                'attr' => array('class' => 'btn btn-sc-red')
            ))
        ;
    }

    public function getName()
    {
        return 'service_civique_import_approval';
    }
}

<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BannerType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', ['label' => 'Bannière (280px x 80px)', 'required' => false])
            ->add('destination', 'url', ['label' => 'Lien cible'])
        ;

        $builder->add('save', 'submit', [
            'label' => 'Valider',
            'attr' => ['class' => 'btn btn-sc-red']
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_banner';
    }
}

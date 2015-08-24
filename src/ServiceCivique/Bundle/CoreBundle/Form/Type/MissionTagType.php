<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
// use ServiceCivique\Bundle\CoreBundle\Entity\Tag;
use Doctrine\ORM\EntityRepository;

class MissionTagType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tag', 'entity', array(
                'class' => 'ServiceCivique\Bundle\CoreBundle\Entity\Tag',
                'query_builder' => function (EntityRepository $repository) {
                   return $repository->createQueryBuilder('q');
                 }
            ));
        ;
    }

    /**
     * {@inheritDoc}
     */
    // public function setDefaultOptions(OptionsResolverInterface $resolver)
    // {
    //     $resolver->setDefaults(array(
    //         'data_class'         => 'ServiceCivique\Bundle\CoreBundle\Entity\Mission',
    //     ));
    // }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_mission_tag';
    }
}

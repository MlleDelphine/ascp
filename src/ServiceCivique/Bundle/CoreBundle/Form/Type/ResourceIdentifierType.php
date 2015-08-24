<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use ServiceCivique\Bundle\CoreBundle\Form\DataTransformer\EntityToIdTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ResourceIdentifierType extends AbstractType
{
    /**
     * Manager registry.
     *
     * @var ManagerRegistry
     */
    protected $manager;

    /**
     * Form name.
     *
     * @var string
     */
    protected $name;

    public function __construct(ManagerRegistry $manager, $name)
    {
        $this->manager = $manager;
        $this->name = $name;
    }

     /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new EntityToIdTransformer($this->manager->getRepository($options['class']), $options['identifier'])
        );
    }

    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'class' => null,
                'identifier' => 'id'
            ))
            ->setAllowedTypes(array(
                'class' => array('string')
            ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}

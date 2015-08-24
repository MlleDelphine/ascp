<?php

namespace ServiceCivique\Bundle\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserFilterType extends AbstractType
{
    protected $requestStack;

    /**
     * @param mixed $requestStack
     */
    public function __construct($requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = $this->requestStack->getMasterRequest();

        $criteria = $request->query->get('criteria');

        $builder
            ->add('type', 'hidden', array(
                'data' => $request->get('type')
            ))
            ->add('query', 'text', array(
                'required' => false,
                // 'label'    => 'sylius.form.user_filter.query',
                'data'     => $criteria && isset($criteria['query']) ? $criteria['query'] : null
            ))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_user_filter';
    }
}

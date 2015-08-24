<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class ApprovalSearchType extends AbstractType
{

    protected $router;

    /**
     * __construct
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('organization_name', null, array(
                'label'    => 'service_civique.form.approval_search.organization.label',
                'required' => false,
                'data'     => isset($options['data']['organization_name']) ? $options['data']['organization_name'] : null
            ))
            ->add('approval_number', null, array(
                'label'    => 'service_civique.form.approval_search.number.label',
                'required' => false,
                'data'     => isset($options['data']['approval_number']) ? $options['data']['approval_number'] : null
            ))
            ->add('search', 'submit', array(
                'label' => 'service_civique.form.approval_search.search.label',
                'attr'  => array('class' => 'btn btn-sc-red btn-lg')
            ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_approval_search';
    }
}

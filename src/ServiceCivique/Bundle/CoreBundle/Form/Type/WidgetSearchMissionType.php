<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContext;

class WidgetSearchMissionType extends AbstractType
{

    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('organization', 'resource_identifier', array(
                'label'      => 'service_civique.organization.form.organization.label',
                'class'      => 'ServiceCiviqueCoreBundle:Organization',
                'identifier' => 'name',
                'required'   => false,
                'attr' => ['disabled' => true]
            ))
            ->add('width', 'text', array('label' => 'Largeur (entre 280 et 500 pixels)', 'required' => false))
        ;

        $builder->add('actions', 'form_actions');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_widget_search_mission';
    }
}

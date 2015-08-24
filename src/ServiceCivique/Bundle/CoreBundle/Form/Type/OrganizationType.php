<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use ServiceCivique\Bundle\CoreBundle\Entity\Organization;
use ServiceCivique\Bundle\AddressingBundle\Form\Type\LocationType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class OrganizationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'service_civique.organization.form.name.label'
            ))
            ->add('approvalNumber', 'service_civique_approval_number', array(
                'label' => 'service_civique.organization.form.approvalNumber.label',
                // 'disabled' => true,
                'attr' => array(
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => 'Entrez ici votre numéro d’agrément. Si vous n’en disposez pas, référez-vous à la page <a tabindex="-1" href="http://www.service-civique.cirrus-cloud.net.fr/page/comment-obtenir-un-agrement">Comment obtenir un agrément</a>. Le numéro d’agrément doit être au format XX-000-00-00000-00. Il est composé de deux lettres, national (NA) ou régional (RG), puis de trois chiffres 000, 2 chiffres indiquant l\'année de la demande initiale, 5 chiffres de numéro de saisie et 2 chiffres indiquant les avenants.'
                )
            ))
            ->add('location', 'location', array(
                'required'           => false,
                'virtual'            => true,
                'precision'          => LocationType::ADDRESS_PRECISION,
                'required_precision' => -1,
                'use_departement'    => false,
                'default_country'    => 'FR'
            ));

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $organization = $event->getData();

                // if organization is not new do nothing
                if (isset($organization) && $organization->getId()) {
                    return;
                }

                // add approvedOrganization fields
                $form->add('approvedOrganization', 'resource_identifier', array(
                    'label'    => 'service_civique.organization.form.approvedOrganization.label',
                    'class'    => 'ServiceCiviqueCoreBundle:Organization',
                    'required' => false
                ))
                ->add('type', 'choice', array(
                    'label'    => 'service_civique.organization.form.type.label',
                    'expanded' => true,
                    'choices'  => array(
                        Organization::TYPE_APPROVED => 'service_civique.organization.form.type.approved.label',
                        Organization::TYPE_HOST     => 'service_civique.organization.form.type.host.label'
                    )
                ));
            }
        );

    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'ServiceCivique\Bundle\CoreBundle\Entity\Organization',
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_organization';
    }
}

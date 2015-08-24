<?php

namespace ServiceCivique\Bundle\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use ServiceCivique\Bundle\AddressingBundle\Form\Type\LocationType;

class NewsletterType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', ['label' => 'Prénom'])
            ->add('lastName', 'text', ['label' => 'Nom'])
            ->add('email', 'text')
            ->add('role', 'choice', [
                'label' => 'Statut',
                'choices' => [
                    User::MISSION_SEEKER_TYPE   => 'En recherche de mission',
                    User::VOLUNTEER_TYPE        => 'Volontaire',
                    User::FORMER_VOLUNTEER_TYPE => 'Ancien volontaire',
                    User::ORGANIZATION_TYPE     => 'Organisme',
                ]
            ])
            ->add('organizationName', 'text', [
                'label' => 'Nom de l\'organisme',
                'required' => false
            ])
            ->add('isNewsletterSubscribed', 'checkbox', array(
                'label'     => 'Recevez les informations du Service Civique',
                'required'  => false
            ))
            ->add('isNewsletterVolunteerSubscribed', 'checkbox', array(
                'label'     => 'Recevoir les newsletters mensuelles à destination des volontaires',
                'required'  => false
            ))
            ->add('location', 'location', array(
                'label'              => 'service_civique.application.form.location.label',
                'required'           => false,
                'virtual'            => true,
                'precision'          => LocationType::ADDRESS_PRECISION,
                'required_precision' => -1,
                'use_departement'    => false,
                'default_country'    => 'FR'
            ))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_newsletter';
    }
}

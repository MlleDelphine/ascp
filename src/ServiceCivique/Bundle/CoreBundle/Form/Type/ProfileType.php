<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Departements\Provider;
use ServiceCivique\Bundle\CoreBundle\Model\Qualification;
use ServiceCivique\Bundle\CoreBundle\Model\Gender;
use ServiceCivique\Bundle\AddressingBundle\Form\Type\LocationType;
use ServiceCivique\Bundle\AddressingBundle\Form\EventListener\AlterDepartementFieldSubscriber;

class ProfileType extends AbstractType
{

    private $departementProvider;

    public function __construct(Provider $departementProvider)
    {
        $this->departementProvider = $departementProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $years = range(date('Y') - 27, date('Y') - 15);
        $locationOptions = array(
            'virtual'            => true,
            'precision'          => LocationType::ADDRESS_PRECISION,
            'required_precision' => -1,
            'use_departement'    => false,
            'default_country'    => 'FR'
        );

        $builder
            ->add('gender', 'choice', array(
                'expanded' => true,
                'label'    => 'service_civique.profile.form.gender.label',
                'choices'  => array(
                    Gender::MALE   => 'service_civique.gender.male',
                    Gender::FEMALE => 'service_civique.gender.female'
                )
            ))
            ->add('birthDate', 'date', array(
                'label'  => 'service_civique.profile.form.birth_date.label',
                'years'  => $years,
                'format' => 'dd-MMMM-yyyy'
            ))
            ->add('motivation', null,  array(
                'label'  => 'service_civique.profile.form.motivation.label',
                'required' => false,
                'attr'     => array(
                    'rows' => 20
                )
            ))
            ->add('phone_number', null,  array(
                'label'  => 'service_civique.profile.form.phone_number.label',
                'required' => false,
            ))
            ->add('educationLevel', 'choice',  array(
                'required' => false,
                'label'    => 'service_civique.profile.form.education_level.label',
                'choices'  => array(
                    Qualification::SANS_QUALIFICATIONS => 'service_civique.education_level.1',
                    Qualification::BREVET              => 'service_civique.education_level.2',
                    Qualification::CAP_BEP             => 'service_civique.education_level.3',
                    Qualification::BAC                 => 'service_civique.education_level.4',
                    Qualification::BAC_PLUS_2          => 'service_civique.education_level.5',
                    Qualification::BAC_PLUS_2_PLUS     => 'service_civique.education_level.6'
                ),
            ))
            ->add('file', 'file', array(
                'label'    => 'service_civique.profile.form.resume.label',
                'required' => false,
            ))
            ->add('location', 'location', $locationOptions);

            $builder->addEventSubscriber(new AlterDepartementFieldSubscriber($this->departementProvider, $locationOptions));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'ServiceCivique\Bundle\CoreBundle\Entity\Profile',
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_profile';
    }
}

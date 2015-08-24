<?php

namespace ServiceCivique\Bundle\AddressingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Departements\Provider;

class LocationType extends AbstractType
{
    const COUNTRY_PRECISION    = 0;
    const DEPARTMENT_PRECISION = 1;
    const CITY_PRECISION       = 2;
    const ADDRESS_PRECISION    = 3;

    protected $departementsProvider;
    protected $router;

    /**
     * __construct
     *
     * @param Provider $departementsProvider
     * @param mixed    $router
     */
    public function __construct(Provider $departementsProvider, $router)
    {
        $this->departementsProvider = $departementsProvider;
        $this->router               = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['precision'] >= self::ADDRESS_PRECISION) {
            $builder
                ->add('address', null, array(
                    'label' => 'service_civique.location.address.label',
                    'required' => $this->isRequired($options['required_precision'], self::ADDRESS_PRECISION)
                ));
        }

        $builder->add('country', 'country', array(
            'empty_value' => 'service_civique.location.country.empty_value',
            'label'       => 'service_civique.location.country.label',
            'required'    => $this->isRequired($options['required_precision'], self::COUNTRY_PRECISION),
            'attr'        => array(
                'class'   => 'country-input'
            ),
            'data' => isset($options['data']) ? $options['data']->getCountry() : $options['default_country']
        ));

        // we need to determine if department and area select needs to be displayed
        // eq: if we required city we don't need ask for department or area
        // if precision greater than or equal to DEPARTMENT_PRECISION
        // and required precision is lower than CITY_PRECISION
        // then display department and area selects
        if ($options['precision'] >= self::DEPARTMENT_PRECISION && $options['required_precision'] <= self::CITY_PRECISION && (is_null($options['use_departement']) || $options['use_departement']) || $options['use_departement']) {

            $area_field_target = uniqid('location_department_');
            $departement_field_target = uniqid('location_area_');

            $builder
                ->add('area', 'polem_region', array(
                    'empty_value' => 'service_civique.location.area.empty_value',
                    'label'       => 'service_civique.location.area.label',
                    'use_codes'   => true,
                    'required'    => $this->isRequired($options['required_precision'], self::DEPARTMENT_PRECISION),
                    'attr' => array(
                        'data-departements-datas-url' => $this->router->generate('polem_departments_list_departements'),
                        'data-target'                 => '.' . $area_field_target,
                        'class'                       => 'area-input '. $departement_field_target
                    )
                ))
                ->add('department', 'polem_departement', array(
                    'empty_value' => 'service_civique.location.department.empty_value',
                    'label'       => 'service_civique.location.department.label',
                    'use_codes'   => true,
                    'region'      => $options['region'],
                    'required'    => $this->isRequired($options['required_precision'], self::DEPARTMENT_PRECISION),
                    'attr'        => array(
                        'data-area-datas-url' => $this->router->generate('service_civique_area_list'),
                        'data-target'                 => '.' . $departement_field_target,
                        'class' => 'departement-input '. $area_field_target
                    )
                ));
        }

        if ($options['precision'] >= self::CITY_PRECISION) {
            $builder
                ->add('zipCode', null, array(
                    'label'    => 'service_civique.location.zip_code.label',
                    'required' => $this->isRequired($options['required_precision'], self::CITY_PRECISION),
                    'attr'     => array(
                        'class'           => 'zipcode-input',
                        'data-cities-url' => $this->router->generate('service_civique_commune_search')
                    )
                ))
                ->add('city', null, array(
                    'label'    => 'service_civique.location.city.label',
                    'required' => $this->isRequired($options['required_precision'], self::CITY_PRECISION),
                    'attr'     => array(
                        'class' => 'city-input',
                    )
                ));

        }
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'precision'          => self::ADDRESS_PRECISION,
            'required_precision' => self::ADDRESS_PRECISION,
            'use_departement'    => null,
            'region'             => null,
            'default_country'    => null,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'location';
    }

    protected function isRequired($required_precision, $precision)
    {
        return $required_precision >= $precision;
    }
}

<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport;

use Ddeboer\DataImport\ItemConverter;
use Ddeboer\DataImport\Filter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\DepartementValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\RegionValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\PhoneNumberValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\FixerValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\OriginalIdToIdValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ItemConverter\MappingItemConverter;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;

/**
 * Class: OrganizationWorkflow
 */
class OrganizationWorkflow extends Workflow
{
    protected $departements_provider;
    protected $phone_number_util;

    public function addFilters()
    {
        $this->addFilter(new Filter\CallbackFilter(function ($data) {
            return isset($data['profile']) && isset($data['profile']['field_structure']) && in_array($data['profile']['field_structure'][0]['value'], array(
                'Une structure à la recherche de volontaires',
                'Une structure a la recherche de volontaires'
            ));
        }));
    }

    public function addItemsConverters()
    {
        $mappingItemConverter = new MappingItemConverter();
        $mappingItemConverter
            ->addMapping('[uid]', '[originalId]')
            ->addMapping('[mail]', '[contact_email]')
            ->addMapping('[name]', '[username]')
            ->addMapping('[profile][field_dept][0][value]', '[department]')
            ->addMapping('[profile][field_regions][0][value]', '[area]')
            ->addMapping('[profile][field_siret][0][value]', '[approvalNumber]')
            ->addMapping('[profile][field_address_structure][0][value]', '[address]')
            ->addMapping('[profile][field_code_postal][0][value]', '[zipCode]')
            ->addMapping('[profile][field_ville][0][value]', '[city]')
            ->addMapping('[profile][field_numero_de_telephone][0][value]', '[phoneNumber]')
            ->addMapping('[profile][field_presenter_votre_structure][0][value]', '[description]')
            ->addMapping('[profile][field_site_internet][0][value]', '[website]')
            ->addMapping('[profile][field_mail_contact_email][0][value]', '[contactEmail]')
            ->addMapping('[profile][field_organisme_agree][0][value]', '[isApproved]')
            ;

        $this->addItemConverter($mappingItemConverter);

        $this->addItemConverter(new ItemConverter\CallbackItemConverter(function ($item) {
            $item['user'] = $item['originalId'];
            $item['type'] = Organization::TYPE_APPROVED;

            // format approval number
            if (isset($item['approvalNumber'])) {
                $item['approvalNumber'] = preg_replace("/[^A-Za-z0-9]/", '', $item['approvalNumber']);

                if (strlen($item['approvalNumber']) == 14 && preg_match("/^[A-Z]{2}/i", $item['approvalNumber']) > 0) {
                    $item['approvalNumber'] = ''.
                        substr($item['approvalNumber'], 0, 2) . '-' .
                        substr($item['approvalNumber'], 2, 3) . '-' .
                        substr($item['approvalNumber'], 4, 2) . '-' .
                        substr($item['approvalNumber'], 7, 5) . '-' .
                        substr($item['approvalNumber'], 12, 2);
                } else {
                    $item['approvalNumber'] = substr($item['approvalNumber'], 0, 18);
                }
            }

            if (isset($item['phoneNumber'])) {
                $item['phoneNumber'] = substr($item['phoneNumber'], 0, 14);
            }

            if (!$item['name'] && isset($item['profile']['field_nom_structure'])) {
                $item['name'] = $item['profile']['field_nom_structure'][0]['value'];
            }

            if (!$item['name'] && isset($item['profile']['field_nom_organisme_ac'])) {
                $item['name'] = $item['profile']['field_nom_organisme_ac'][0]['value'];
            }

            if (!$item['name'] && isset($item['profile']['field_organisme_agree'])) {
                $item['name'] = $item['profile']['field_organisme_agree'][0]['value'];
            }

            if (!$item['name']) {
                $item['name'] = '';
            }

            return $item;
        }));
    }

    public function addValueConverters()
    {
        $connection = $this->getEntityManager()->getConnection();
        $departementsProvider = $this->getDepartementsProvider();
        $phoneNumberUtil = $this->getPhoneNumberUtil();

        $departementsFixes = array(
            'Réunion'                 => 'La Réunion',
            'Tous les départements'   => null,
            'Choisissez'              => null,
            'N\'habite pas en France' => null
        );

        $areasFixes = array(
            'Bourgagne'               => 'Bourgogne',
            'Réunion'                 => 'La Réunion',
            'Languedoc-Roussilon'     => 'Languedoc-Roussillon',
            'Champagne-Ardene'        => 'Champagne-Ardenne',
            'N\'habite pas en France' => null,
            'Toutes les régions'      => null,
            'Choisissez'              => null
        );

        $booleanFixer = new FixerValueConverter(array(
            'Oui' => 1,
            'Non' => 0
        ));

        $this
            ->addValueConverter('isApproved', $booleanFixer)
            ->addValueConverter('department', new FixerValueConverter($departementsFixes))
            ->addValueConverter('department', new DepartementValueConverter($departementsProvider))
            ->addValueConverter('area', new FixerValueConverter($areasFixes))
            ->addValueConverter('area', new RegionValueConverter($departementsProvider))
            ->addValueConverter('phoneNumber', new PhoneNumberValueConverter($phoneNumberUtil))
            ->addValueConverter('user', new OriginalIdToIdValueConverter($connection, 'user'));
    }

    public function getDepartementsProvider()
    {
        return $this->departements_provider;
    }

    public function setDepartementsProvider($departements_provider)
    {
        $this->departements_provider = $departements_provider;

        return $this;
    }

    public function setPhoneNumberUtil(\libphonenumber\PhoneNumberUtil $phone_number_util)
    {
        $this->phone_number_util = $phone_number_util;

        return $this;
    }

    public function getPhoneNumberUtil()
    {
        return $this->phone_number_util;
    }

    protected function getEntityClass()
    {
        return 'ServiceCiviqueCoreBundle:Organization';
    }

}

<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport;

use Ddeboer\DataImport\ItemConverter;
use Ddeboer\DataImport\ValueConverter;
use Ddeboer\DataImport\Filter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ItemConverter\MappingItemConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\FixerValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\DepartementValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\RegionValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\OriginalIdToIdValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\CvFilePathValueConverter;
use ServiceCivique\Bundle\CoreBundle\Model\Qualification;

class ProfileWorkflow extends Workflow
{
    protected $departements_provider;

    public function addFilters()
    {
        $this->addFilter(new Filter\CallbackFilter(function ($data) {
            $values = array(
                'En recherche dun volontariat',
                'En recherche d\'un volontariat',
                'Ancien volontaire',
                'Volontaire'
            );

            return isset($data['profile']) && in_array($data['profile']['field_structure'][0]['value'], $values);
        }));
    }

    public function addItemsConverters()
    {
        $mappingItemConverter = new MappingItemConverter();
        $mappingItemConverter
            ->addMapping('[nid]', '[originalId]')
            ->addMapping('[uid]', '[user]')
            ->addMapping('[profile][changed]', '[updated]')
            ->addMapping('[profile][field_profil_benefit_allocation][0][value]', '[AAH]')
            ->addMapping('[profile][field_profil_benefit_reconnaissa][0][value]', '[RQTH]')
            ->addMapping('[profile][field_niveau_edute][0][value]', '[educationLevel]')
            ->addMapping('[profile][field_inscrit_newsletter][0][value]', '[isNewsletterSubscribed]')
            ->addMapping('[profile][field_info_civique][0][value]', '[receiveInformations]')
            ->addMapping('[profile][field_consultable][0][value]', '[isPublic]')
            ->addMapping('[profile][field_sex][0][value]', '[gender]')
            ->addMapping('[profile][field_user_age][0][value]', '[birthDate]')
            ->addMapping('[profile][field_code_postal][0][value]', '[zipCode]')
            ->addMapping('[profile][field_dept][0][value]', '[department]')
            ->addMapping('[profile][field_regions][0][value]', '[area]')
            ->addMapping('[curriculum][filepath]', '[cv]')
            ->addMapping('[motivation]', '[motivation]')
            ;

        $this->addItemConverter($mappingItemConverter);
        $this->addItemConverter(new ItemConverter\CallbackItemConverter(function ($item) {

            $item['isApproved'] = 'Oui';

            if (!isset($item['receiveInformations'])) {
                $item['receiveInformations'] = 'Non';
            }

            if (!isset($item['isPublic'])) {
                $item['isPublic'] = 'Non';
            }

            // if birthDate is not set
            if (!isset($item['birthDate']) || !$item['birthDate']) {
                $item['birthDate'] = null;

                if (isset($item['profile']['field_age']) && $item['profile']['field_age'][0]['value']) {
                    // if age < 100
                    if ($item['profile']['field_age'][0]['value'] < 100) {
                        try {
                            $item['birthDate'] = date('Y-m-d\T00:00:00', strtotime(' - '.$item['profile']['field_age'][0]['value'] .'years', $item['created']));
                        } catch (\UnexpectedValueException $e) {
                            $this->logger->error($e->getMessage());
                        }
                    }
                }
            }

            if ($item['birthDate']) {
                try {
                    $item['birthDate'] = \DateTime::createFromFormat('Y-m-d\T00:00:00', $item['birthDate']);
                } catch (\UnexpectedValueException $e) {
                    $this->logger->error($e->getMessage());
                }
            }

            return $item;
        }));
    }

    public function addValueConverters()
    {
        $departementsProvider = $this->getDepartementsProvider();

        $connection = $this->getEntityManager()->getConnection();

        $booleanFixer = new FixerValueConverter(array(
            'Oui' => 1,
            'Non' => 0,
            null  => 0,
            ''    => 0
        ));

        $sexFixer = new FixerValueConverter(array(
            'un garçon' => 0,
            'un garcon' => 0,
            'une fille' => 1,
            ''          => 0
        ));

        $educationLevelFixer = new FixerValueConverter(array(
            "Sans qualifications"                               => Qualification::SANS_QUALIFICATIONS,
            "BEP/CAP"                                           => Qualification::CAP_BEP,
            "Brevet"                                            => Qualification::BREVET,
            "Bac"                                               => Qualification::BAC,
            "Master"                                            => Qualification::BAC_PLUS_2_PLUS,
            "Licence Pro"                                       => Qualification::BAC_PLUS_2_PLUS,
            "Licence"                                           => Qualification::BAC_PLUS_2_PLUS,
            "BTS/DUT"                                           => Qualification::BAC_PLUS_2,
            "bac+5"                                             => Qualification::BAC_PLUS_2_PLUS,
            "Bac+3 licence science politique Université Lyon 2" => Qualification::BAC_PLUS_2_PLUS,
            "BEP"                                               => Qualification::CAP_BEP,
            "Doctorat"                                          => Qualification::BAC_PLUS_2_PLUS,
            ""                                                  => Qualification::SANS_QUALIFICATIONS
        ));

        $departementsFixes = array(
            'Réunion'                 => 'La Réunion',
            'Tous les départements'   => null,
            'Choisissez'              => null,
            'N\'habite pas en France' => null,
            'Guadaloupe'              => 'Guadeloupe',
            'TAAF'                    => 'Terres australes et antarctiques françaises',
            'Ile-de-Clipperton'       => null
        );

        $areasFixes = array(
            'Ile-de-Clipperton'       => null,
            'TAAF'                    => 'Terres australes et antarctiques françaises',
            'Bourgagne'               => 'Bourgogne',
            'Guadaloupe'              => 'Guadeloupe',
            'Réunion'                 => 'La Réunion',
            'Languedoc-Roussilon'     => 'Languedoc-Roussillon',
            'Champagne-Ardene'        => 'Champagne-Ardenne',
            'N\'habite pas en France' => null,
            'Toutes les régions'      => null,
            'Choisissez'              => null
        );

        $this
            ->addValueConverter('isPublic', $booleanFixer)
            ->addValueConverter('AAH', $booleanFixer)
            ->addValueConverter('RQTH', $booleanFixer)
            ->addValueConverter('isNewsletterSubscribed', $booleanFixer)
            ->addValueConverter('receiveInformations', $booleanFixer)
            ->addValueConverter('educationLevel', $educationLevelFixer)
            ->addValueConverter('gender', $sexFixer)
            ->addValueConverter('cv', new CvFilePathValueConverter())
            ->addValueConverter('department', new FixerValueConverter($departementsFixes))
            ->addValueConverter('department', new DepartementValueConverter($departementsProvider))
            ->addValueConverter('area', new FixerValueConverter($areasFixes))
            ->addValueConverter('area', new RegionValueConverter($departementsProvider))
            ->addValueConverter('updated', new ValueConverter\DateTimeValueConverter('U'))
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

    protected function getEntityClass()
    {
        return 'ServiceCiviqueCoreBundle:Profile';
    }
}

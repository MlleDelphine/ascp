<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport;

use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use Ddeboer\DataImport\ItemConverter;
use Ddeboer\DataImport\ValueConverter;
use Ddeboer\DataImport\Filter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\DepartementValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\RegionValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\PhoneNumberValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\FixerValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\OriginalIdToIdValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\UrlFixerValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ItemConverter\MappingItemConverter;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * Class: MissionWorkflow
 */
class MissionWorkflow extends Workflow
{
    protected $departements_provider;
    protected $phone_number_util;
    protected $taxonIndex;

    public function addFilters()
    {
        $this->addFilter(new Filter\CallbackFilter(function ($data) {
            return isset($data['field_date_debut']) && $data['field_date_debut'][0]['value'];
        }));

        $this->addFilterAfterConversion(new Filter\CallbackFilter(function ($data) {
            return isset($data['organization']);
        }));
    }

    public function addItemsConverters()
    {
        $mappingItemConverter = new MappingItemConverter();
        $mappingItemConverter
            ->addMapping('[nid]', '[originalId]')
            ->addMapping('[uid]', '[organization]')
            ->addMapping('[field_date_debut][0][value]', '[startDate]')
            ->addMapping('[field_date_debut][0][value]', '[startDate]')
            ->addMapping('[field_duree_mission][0][value]', '[duration]')
            ->addMapping('[field_duree_hebdomadaire][0][value]', '[weeklyWorkingHours]')
            ->addMapping('[field_telephone_contact][0][value]', '[phoneNumber]')
            ->addMapping('[field_site_web_structure][0][value]', '[website]')
            ->addMapping('[field_address_structure][0][value]', '[address]')
            ->addMapping('[field_presasso][0][value]', '[organizationDescription]')
            ->addMapping('[field_numero_agrement][0][value]', '[approvalNumber]')
            ->addMapping('[field_ville][0][value]', '[city]')
            ->addMapping('[field_departement][0][value]', '[department]')
            ->addMapping('[field_regions_mission][0][value]', '[area]')
            ->addMapping('[field_nom_contact][0][value]', '[contact]')
            ->addMapping('[field_cod_postal][0][value]', '[zipCode]')
            ->addMapping('[field_nombre_poste][0][value]', '[vacancies]')
            ->addMapping('[field_lieux_mission][0][value]', '[country]')
            ->addMapping('[field_nom_structure][0][value]', '[organizationName]')
            ->addMapping('[changed]', '[updated]')
            ->addMapping('[body]', '[description]')
            ;

        $this->addItemConverter($mappingItemConverter);

        $this->addItemConverter(new ItemConverter\CallbackItemConverter(function ($item) {

            $item['published'] = $item['updated'];

            $item['taxons'] = array();

            $item['slug'] = $item['originalId'] . '-' . Urlizer::urlize($item['title']);

            foreach ($item['taxonomy'] as $term) {
                if ($term['vid'] == 1) {
                    $tid = $this->findCategoryTaxonIdByName($term['name']);
                    if ($tid) {
                        //$item['taxon'] = $tid;
                         $item['taxon'] = $this->getEntityManager()->getReference('ServiceCiviqueCoreBundle:Taxon', $tid);
                    }
                }

                if ($term['tid'] == 16) {
                    $item['country'] = 'France';
                    $item['isOverseas'] = false;
                }

                if ($term['tid'] == 17) {
                    $item['isOverseas'] = true;
                }

                if ($item['status'] != 0) {
                    // "Mission pourvue"
                    if ($term['tid'] == 44) {
                        $item['status'] = Mission::STATUS_FILLED;
                    }

                    // "Mission à pourvoir"
                    if ($term['tid'] == 20) {
                        $item['status'] = Mission::STATUS_AVAILABLE;
                    }
                }
            }

            // "Mission pourvue"
            if ($item['status'] == 0) {
                $item['status'] = Mission::STATUS_UNDER_REVIEW;
            }

            $item['country']          = isset($item['country']) && Urlizer::urlize($item['country']) != "" ? $item['country'] : 'France';

            if (!isset($item['organizationName']) || trim($item['organizationName']) == "") {
                $item['organizationName'] = $this->getEntityManager()->getConnection()->fetchColumn('SELECT name FROM organization WHERE original_id = ?', array((int) $item['organization']), 0);
            }

            // add default value
            $item['weeklyWorkingHours'] = isset($item['weeklyWorkingHours']) ? $item['weeklyWorkingHours'] : 35;
            $item['vacancies']          = isset($item['vacancies']) ? $item['vacancies'] : 1;
            $item['duration']           = isset($item['duration']) ? $item['duration'] : 6;
            $item['contact']            = isset($item['contact']) ? $item['contact'] : '';

            return $item;
        }));
    }

    public function addValueConverters()
    {
        $departementsProvider = $this->getDepartementsProvider();
        $phoneNumberUtil = $this->getPhoneNumberUtil();

        $departementsFixes = array(
            'Réunion'               => 'La Réunion',
            'Tous les départements' => null
        );

        $areasFixes = array(
            'Réunion'             => 'La Réunion',
            'Languedoc-Roussilon' => 'Languedoc-Roussillon',
            'Toutes les régions'  => null
        );

        $countryFixes = array(
            'guyane'                                   => 'Guyane francaise',
            'angleterre'                               => 'Royaume-Uni',
            'vietnam'                                  => 'Viet Nam',
            'congo'                                    => 'Republique Democratique du Congo',
            'republique du congo'                      => 'Republique Democratique du Congo',
            ''                                         => 'France',
            'italy'                                    => 'Italie',
            'Québec, Canada'                           => 'Canada',
            'Nouveau-Brunswick, Canada'                => 'Canada',
            'Nouvelle-Ecosse, Canada'                  => 'Canada',
            'CAMEROUN - Yaoundé'                       => 'Cameroun',
            'BURUNDI - Bujumbura et Gatumba'           => 'Burundi',
            'TOGO - Lomé'                              => 'Togo',
            'TUNISIE - Tunis'                          => 'Tunisie',
            'ALBANIE - Tirana'                         => 'Albanie',
            'RWANDA - Kigali et provinces intérieures' => 'Rwanda',
            'Corée'                                    => 'Corée du sud',
            'Cambridge, Royaume-Uni'                   => 'Royaume-Uni',
            'Allemagne-Lüneburg'                       => 'Allemagne',
            'Alemagne - Göttingen'                     => 'Allemagne',
            'Allemagne - Göttingen'                    => 'Allemagne',
            'Alemagne - Göttingen'                     => 'Allemagne',
            'Burkina faso ( Bobo Dioulassou)'          => 'Burkina faso',
            'Pérou (Nazca)'                            => 'Pérou',
            'HAITI- Port au Prince'                    => 'Haiti',
            'République de Macédoine - Skopje'         => 'Macédoine',
            'San Sebastian, ESPAGNE'                   => 'Espagne',
            'Dakar (Sénégal)'                          => 'Sénégal',
            'Bangkok'                                  => 'Inde',
            'RWANDA - Kigali'                          => 'Rwanda',
            'BURUNDI- Bajumbara'                       => 'Burundi',
            'Royaume Uni  Londres'                     => 'Royaume-Uni',
            'TOGO- Lomé'                               => 'Togo'
        );

        $countryNames = \Symfony\Component\Intl\Intl::getRegionBundle()->getCountryNames();
        $countryNamesIndex = array_flip(array_map(function ($value) {
            return Urlizer::urlize($value);
        }, $countryNames));

        $connection = $this->getEntityManager()->getConnection();

        $this
            ->addValueConverter('startDate', new ValueConverter\DateTimeValueConverter('Y-m-d\TH:i:s'))
            ->addValueConverter('created', new ValueConverter\DateTimeValueConverter('U'))
            ->addValueConverter('updated', new ValueConverter\DateTimeValueConverter('U'))
            ->addValueConverter('published', new ValueConverter\DateTimeValueConverter('U'))
            ->addValueConverter('department', new FixerValueConverter($departementsFixes))
            ->addValueConverter('department', new DepartementValueConverter($departementsProvider))
            ->addValueConverter('area', new FixerValueConverter($areasFixes))
            ->addValueConverter('area', new RegionValueConverter($departementsProvider))
            ->addValueConverter('phoneNumber', new PhoneNumberValueConverter($phoneNumberUtil))
            ->addValueConverter('description', new ValueConverter\CallbackValueConverter(function ($value) {
                return html_entity_decode(strip_tags($value));
            }))
            ->addValueConverter('organization', new OriginalIdToIdValueConverter($connection, 'organization'))
            ->addValueConverter('website', new UrlFixerValueConverter())
            ->addValueConverter('country', new FixerValueConverter($countryFixes))
            ->addValueConverter('country', new ValueConverter\CallbackValueConverter(function ($value) use ($countryNamesIndex) {

                    if (!isset($value) || !$value) {
                        return 'FR';
                    }

                    $value = Urlizer::urlize($value);

                    if (!isset($countryNamesIndex[$value])) {
                        return null;
                    }

                    return $countryNamesIndex[$value];
            }));
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
        return 'ServiceCiviqueCoreBundle:Mission';
    }

    public function findCategoryTaxonIdByName($name)
    {
        if (!$this->taxonIndex) {
            $em = $this->getEntityManager();
            $query = $em->createQuery('SELECT t.name, t.id FROM ServiceCiviqueCoreBundle:Taxon t INDEX BY t.name');
            $this->taxonIndex = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }

        return isset($this->taxonIndex[$name]) ? $this->taxonIndex[$name]['id'] : null;
    }

}

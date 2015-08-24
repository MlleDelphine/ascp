<?php

namespace ServiceCivique\Bundle\CoreBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;
use Departements\Provider;

class MissionSearchRepository extends EntityRepository
{
    protected $departementProvider;

    /**
     * setDepartementProvider
     *
     * @param Provider $departementProvider
     */
    public function setDepartementProvider(Provider $departementProvider)
    {
        $this->departementProvider = $departementProvider;

        return $this;
    }

    /**
     * create a new missionsearch from array
     *
     * @param  mixed                   $criteria
     * @return MissionSearchRepository $mission
     */
    public function createNewFromArray($criteria = array())
    {
        $missionSearch = $this->createNew();
        $this->populateWithCriteria($missionSearch, $criteria);

        return $missionSearch;
    }

    public function populateWithCriteria($missionSearch, $criteria = array())
    {
        $defaultCriteria = array(
            'is_overseas'  => false,
            'country'      => null,
            'area'         => null,
            'department'   => null,
            'start_date'   => date('Y-m-d'),
            'published'    => null,
            'taxons'       => array(),
            'query'        => null,
            'statuses'     => array(),
            'organization' => null,
            'tag'          => null
        );

        $criteria = array_merge($defaultCriteria, $criteria);

        $missionSearch->setIsOverseas($criteria['is_overseas']);
        $missionSearch->setStartDate(new \DateTime($criteria['start_date']));
        $missionSearch->setPublished(new \DateTime($criteria['published']));
        $missionSearch->setQuery($criteria['query']);
        $missionSearch->setCountry($criteria['country']);
        $missionSearch->setArea($criteria['area']);
        $missionSearch->setDepartment($criteria['department']);
        $missionSearch->setOrganization($criteria['organization']);

        if ($criteria['tag']) {
            $missionSearch->setTag($this->getEntityManager()->getReference('ServiceCiviqueCoreBundle:Tag', $criteria['tag']));
        }

        $taxons = $missionSearch->getTaxons();
        foreach ($taxons as $taxon) {
            $missionSearch->removeTaxon($taxon);
        }

        foreach ($criteria['taxons'] as $taxon) {
            $taxon = $this->getEntityManager()->getReference('ServiceCiviqueCoreBundle:Taxon', $taxon);
            $missionSearch->addTaxon($taxon);
        }

        $missionSearch->setStatuses($criteria['statuses']);

        return $missionSearch;
    }
}

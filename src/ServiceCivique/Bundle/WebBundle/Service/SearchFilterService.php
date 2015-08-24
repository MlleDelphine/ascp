<?php

namespace ServiceCivique\Bundle\WebBundle\Service;

use ServiceCivique\Bundle\CoreBundle\Entity\MissionSearch;
use Symfony\Component\Intl\Intl;

class SearchFilterService
{
    protected $departmentProvider;

    public function __construct($departmentProvider)
    {
        $this->departmentProvider = $departmentProvider;
    }

    public function getMissionSearchActiveFilters(MissionSearch $missionSearch, $params = array())
    {
        $filters = array();

        if ($missionSearch->getIsOverseas() !== null) {
            $labels = [
                0 => 'service_civique.form.mission_search.is_overseas.value_france',
                1 => 'service_civique.form.mission_search.is_overseas.value_foreign'
            ];

            $filters[] = [
                'id'            => 'is_overseas',
                'default_value' => 0,
                'label'         => $labels[$missionSearch->getIsOverseas()],
                'value'         => $missionSearch->getIsOverseas(),
                'children'      => array('country', 'area', 'department')
            ];
        }

        if ($missionSearch->getCountry()) {
            $filters[] = [
                'id'       => 'country',
                'label'    => Intl::getRegionBundle()->getCountryName($missionSearch->getCountry()),
                'value'    => $missionSearch->getCountry(),
                'children' => array('area', 'department')
            ];
        }

        if ($missionSearch->getArea()) {
            $area = $this->departmentProvider->findRegionByCode($missionSearch->getArea());

            if ($area) {
                $filters[] = [
                    'id'       => 'area',
                    'label'    => $area->getName(),
                    'value'    => $area->getCode(),
                    'children' => array('department')
                ];
            }
        }

        if ($missionSearch->getDepartment()) {
            $department = $this->departmentProvider->findDepartementByCode($missionSearch->getDepartment());

            if ($department) {
                $filters[] = [
                    'id'       => 'department',
                    'label'    => $department->getName(),
                    'value'    => $department->getCode(),
                ];
            }
        }

        if ($missionSearch->getStartDate()) {
            $filters[] = [
                'id'            => 'start_date',
                'default_value' => new \DateTime(date('Y-m-d')),
                'label'         => 'À partir du ' . $missionSearch->getStartDate()->format('d/m/Y'),
                'value'         => $missionSearch->getStartDate()
            ];
        }

        if ($missionSearch->getTaxons()) {
            $termIteration = 0;
            foreach ($missionSearch->getTaxons() as $term) {
                $filters[] = [
                    'id'    => 'taxons',
                    'label' => $term->getName(),
                    'value' => $termIteration++,
                ];
            }
        }

        if ($missionSearch->getQuery()) {
            $filters[] = [
                'id'    => 'query',
                'label' => 'Contient ' . $missionSearch->getQuery(),
                'value' => $missionSearch->getQuery()
            ];
        }

        if ($missionSearch->getOrganization()) {
            $filters[] = [
                'id'    => 'organization',
                'label' => 'Organisme : ' . $missionSearch->getOrganization(),
                'value' => $missionSearch->getOrganization()
            ];
        }

        // build filter $params
        foreach ($filters as &$filter) {
            $excludedParams = isset($filter['children']) ? $filter['children'] : array();
            $excludedParams[] = $filter['id'];
            unset($params['page']);
            foreach ($params as $key => $param) {
                if (in_array($key, $excludedParams)) {
                    if (!is_array($param)) {
                        continue;
                    }
                    unset($param[$filter['value']]);
                    $param = array_values($param);
                }

                $filter['params'][$key] = $param;
            }
        }

        return $filters;
    }
}

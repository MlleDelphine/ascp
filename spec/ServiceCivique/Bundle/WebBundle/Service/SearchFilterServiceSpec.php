<?php

namespace spec\ServiceCivique\Bundle\WebBundle\Service;

use PhpSpec\ObjectBehavior;
use ServiceCivique\Bundle\CoreBundle\Entity\MissionSearch;
use Departements\Model\Region;
use Departements\Model\Departement;
use Departements\Provider;
use Prophecy\Prophet;

class SearchFilterServiceSpec extends ObjectBehavior
{
    function let($provider)
    {
        $prophet = new Prophet();

        $provider->beADoubleOf('Departements\Provider');

        $region      = $prophet->prophesize('Departements\Model\Region');
        $departement = $prophet->prophesize('Departements\Model\Departement');

        $region->getName()->willReturn('Île-de-France');
        $region->getCode()->willReturn('11');

        $departement->getName()->willReturn('Paris');
        $departement->getCode()->willReturn(75);

        $provider->findDepartementByCode(75)->willReturn($departement);
        $provider->findRegionByCode('11')->willReturn($region);

        $this->beConstructedWith($provider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ServiceCivique\Bundle\WebBundle\Service\SearchFilterService');
    }

    function it_should_return_active_filters_given_a_mission_search(MissionSearch $missionSearch, Departement $departement, Region $region)
    {
        $prophet = new Prophet();

        $taxon1 = $prophet->prophesize('Sylius\Component\Taxonomy\Model\Taxon');
        $taxon2 = $prophet->prophesize('Sylius\Component\Taxonomy\Model\Taxon');

        $taxon1->getName()->willReturn('Taxon 1');
        $taxon1->getId()->willReturn(3);

        $taxon2->getName()->willReturn('Taxon 2');
        $taxon2->getId()->willReturn(4);

        $startDate = new \Datetime('2014-05-01');

        $params = array(
            'start_date' => '2014-05-01',
            'area'       => 11,
            'department' => 75,
            'taxons'     => array(3, 4),
            'page'       => 1
        );

        $missionSearch->getTaxons()->willReturn([$taxon1, $taxon2]);
        $missionSearch->getStartDate()->willReturn($startDate);
        $missionSearch->getQuery()->willReturn('foo');
        $missionSearch->getArea()->willReturn(11);
        $missionSearch->getCountry()->willReturn('FR');
        $missionSearch->getIsOverseas()->willReturn(null);
        $missionSearch->getDepartment()->willReturn(75);
        $missionSearch->getOrganization()->willReturn('Organization');

        $expectedFilters = [
            [
                'id'       => 'country',
                'label'    => 'France',
                'value'    => 'FR',
                'children' => array('area', 'department'),
                'params'   => array('start_date' => '2014-05-01', 'taxons' => array(3, 4))
            ],
            [
                'id'       => 'area',
                'label'    => 'Île-de-France',
                'value'    => 11,
                'children' => array('department'),
                'params'   => array('start_date'  => '2014-05-01', 'taxons' => array(3, 4))
            ],
            [
                'id'     => 'department',
                'label'  => 'Paris',
                'value'  => 75,
                'params' => array('start_date' => '2014-05-01', 'area' => 11, 'taxons' => array(3, 4))
            ],
            [
                'id'            => 'start_date',
                'label'         => 'À partir du 01/05/2014',
                'value'         => $startDate,
                'default_value' => new \Datetime(date('Y-m-d')),
                'params'        => array('area' => 11, 'department' => 75, 'taxons' => array(3, 4))
            ],
            [
                'id'     => 'taxons',
                'label'  => 'Taxon 1',
                'value'  => 0,
                'params' => array('start_date' => '2014-05-01', 'area' => 11, 'department' => 75, 'taxons' => array(4))
            ],
            [
                'id'     => 'taxons',
                'label'  => 'Taxon 2',
                'value'  => 1,
                'params' => array('start_date' => '2014-05-01', 'area' => 11, 'department' => 75, 'taxons' => array(3))
            ],
            [
                'id'     => 'query',
                'label'  => 'Contient foo',
                'value'  => 'foo',
                'params' => array('start_date' => '2014-05-01', 'area' => 11, 'department' => 75, 'taxons' => array(3, 4))
            ],
            [
                'id'     => 'organization',
                'label'  => 'Organisme : Organization',
                'value'  => 'Organization',
                'params' => array('start_date' => '2014-05-01', 'area' => 11, 'department' => 75, 'taxons' => array(3, 4))
            ]
        ];

        $this->getMissionSearchActiveFilters($missionSearch, $params)->shouldBeLike($expectedFilters);
    }
}

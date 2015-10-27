<?php

namespace spec\ServiceCivique\Bundle\SeoBundle;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BreadcrumbGeneratorSpec extends ObjectBehavior
{
    function let()
    {
        $config = array(
            'route_1' => array(
                'title' => 'title 1'
            ),
            'route_2' => array(
                'title'  => 'title 2',
                'parent' => 'route_1'
            ),
            'route_3' => array(
                'title'  => 'title 3',
                'parent' => 'route_2'
            )
        );

        $this->beConstructedWith($config);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ServiceCivique\Bundle\SeoBundle\BreadcrumbGenerator');
    }

    function it_should_return_breadcrumb_items() {

        $expected = array(
            array(
                'name'  => 'title 1',
                'route' => array(
                    'route_1',
                    array(
                        'param_key' => 'param_value'
                    )
                )
            ),
            array(
                'name'  => 'title 2',
                'route' => array('route_2', null)
            ),
            array(
                'name'  => 'title 3',
                'route' => array('route_3', null)
            )
        );

        $routeParams = array('route_1' => array(
            'param_key' => 'param_value'
        ));

        $this->getBreadcrumbItems('route_3', $routeParams)->shouldReturn($expected);
    }
}

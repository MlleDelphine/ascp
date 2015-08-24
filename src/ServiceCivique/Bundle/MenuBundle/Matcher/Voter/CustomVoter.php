<?php

namespace ServiceCivique\Bundle\MenuBundle\Matcher\Voter;

use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ServiceCivique\Bundle\MenuBundle\RouteMapMatcher;

class CustomVoter implements VoterInterface
{
    protected $requestStack;
    protected $mappedRouteName;

    /**
     * __construct
     *
     * @param RequestStack          $requestStack
     * @param UrlGeneratorInterface $urlGenerator
     * @param RouteMapMatcher       $routeMapMatcher
     */
    public function __construct(
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator,
        RouteMapMatcher $routeMapMatcher
    )
    {
        $this->requestStack    = $requestStack;
        $this->urlGenerator    = $urlGenerator;
        $this->routeMapMatcher = $routeMapMatcher;
    }

    public function matchItem(ItemInterface $item)
    {
        $mappedRouteName = $this->getMappedRouteName();

        if (!$mappedRouteName) {
            return false;
        }

        return $this->matchItemAgainstRouteName($item, $mappedRouteName);
    }

    public function getMappedRouteName()
    {
        if (!$this->mappedRouteName) {
            $this->mappedRouteName = $this->retreiveMappedRouteName();
        }

        return $this->mappedRouteName;
    }

    protected function retreiveMappedRouteName()
    {
        $request = $this->requestStack->getCurrentRequest();

        $routeName = $request->get('_route');

        return $this->routeMapMatcher->match($request->get('_route'));
    }

    protected function matchItemAgainstRouteName(ItemInterface $item, $routeName)
    {
        return rtrim($item->getUri(), '/') == rtrim($this->urlGenerator->generate($routeName), '/');
    }

    protected function getRouteMapping()
    {
        return [
            'service_civique_actu_show'      => 'service_civique_actu_list',
            'service_civique_presse_show'    => 'service_civique_presse_list',
            'service_civique_partner_show'   => 'service_civique_partner_list',
            'service_civique_advantage_show' => 'service_civique_advantage_list',
            'service_civique_media_show'     => 'service_civique_media_list',
            'service_civique_mission_show'   => 'service_civique_mission_list',
            'service_civique_video_show'     => 'service_civique_video_list'
        ];
    }
}

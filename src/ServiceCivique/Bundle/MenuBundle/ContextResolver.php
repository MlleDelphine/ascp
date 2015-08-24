<?php

namespace ServiceCivique\Bundle\MenuBundle;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContextResolver
{
    protected $requestStack;

    /**
     * @var RepositoryInterface
     */
    protected $menuItemRepository;

    /**
     * routeMapMatcher
     *
     * @var RouteMapMatcher
     */
    protected $routeMapMatcher;

    protected $context;

    public function __construct(
        RequestStack $requestStack,
        ObjectRepository $menuRepository,
        RouteMapMatcher $routeMapMatcher,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->requestStack    = $requestStack;
        $this->menuRepository  = $menuRepository;
        $this->routeMapMatcher = $routeMapMatcher;
        $this->urlGenerator    = $urlGenerator;
    }

    public function getContext()
    {
        if (!$this->context) {
            $this->context = $this->retreiveContext();
        }

        return $this->context;
    }

    protected function retreiveContext()
    {
        $request = $this->requestStack->getCurrentRequest();

        $uri = $request->getPathInfo();

        if (preg_match('/^\/organismes/', $uri)) {
            return 'organization';
        } elseif (preg_match('/^\/a-propos-de-nous/', $uri)) {
            return 'corporate';
        } elseif ($uri != '/') {
            $menu = $this->getCurrentMenu();

            if ($menu) {
                return $menu->getSlug();
            }
        }

        return 'jeunes';
    }

    public function getCurrentMenu()
    {
        $request = $this->requestStack->getCurrentRequest();

        $uri = $request->getPathInfo();

        if ($mappedRoute = $this->routeMapMatcher->match($request->get('_route'))) {
            $baseUrl = $request->getBaseUrl();
            $uri = str_replace($baseUrl, '', $this->urlGenerator->generate($mappedRoute));
        }

        return $this->menuRepository->findOneByUri($uri);
    }

    public function isHomePage()
    {
        $uri = $this->requestStack->getCurrentRequest()->getPathInfo();

        return ($uri == '/');
    }

}

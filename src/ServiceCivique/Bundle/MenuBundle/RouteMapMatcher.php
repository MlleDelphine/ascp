<?php

namespace ServiceCivique\Bundle\MenuBundle;

class RouteMapMatcher
{
    /**
     * routeMapping
     *
     * @var array
     */
    protected $routeMapping;

    /**
     * __construct
     *
     * @param array $routeMapping
     */
    public function __construct(array $routeMapping)
    {
        $this->routeMapping = $routeMapping;
    }

    /**
     * match route and return mapped route name
     *
     * @param  string $routeName
     * @return string $mappedRoute
     */
    public function match($routeName)
    {
        return isset($this->routeMapping[$routeName]) ? $this->routeMapping[$routeName] : false;
    }
}

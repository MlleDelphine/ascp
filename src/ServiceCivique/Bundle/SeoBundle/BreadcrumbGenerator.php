<?php

namespace ServiceCivique\Bundle\SeoBundle;

class BreadcrumbGenerator
{
    /**
     * @param mixed $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getBreadcrumbItems($routeName, $routesParams)
    {
        $tree = $this->buildTree($routeName, $routesParams);

        return array_reverse($tree);
    }

    protected function buildTree($routeName, $routesParams, $tree = array())
    {
        if (!isset($this->config[$routeName])) {
            return $tree;
        }

        $routeParams = isset($routesParams[$routeName]) ? $routesParams[$routeName] : null;

        $tree[] = array(
            'name'  => $this->config[$routeName]['title'],
            'route' => array(
                $routeName,
                $routeParams
            )
        );

        if (isset($this->config[$routeName]['parent'])) {
            return $this->buildTree($this->config[$routeName]['parent'], $routesParams, $tree);
        }

        return $tree;
    }

}

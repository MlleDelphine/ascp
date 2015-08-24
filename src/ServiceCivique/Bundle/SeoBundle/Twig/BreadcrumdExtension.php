<?php

namespace ServiceCivique\Bundle\SeoBundle\Twig;

use ServiceCivique\Bundle\SeoBundle\BreadcrumbGenerator;

class BreadcrumdExtension extends \Twig_Extension
{
    /**
     * @param BreadcrumbGenerator $breadcrumbGenerator
     */
    public function __construct(BreadcrumbGenerator $breadcrumbGenerator)
    {
        $this->breadcrumbGenerator = $breadcrumbGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('service_civique_breadcrumb', array($this, 'breadcrumbFunction'), array(
                'needs_environment' => true,
                'is_safe'           => array('html')
            ))
        );
    }

    /**
     * breadcrumbFunction
     *
     * @param Twig_Environment $env
     * @param string           $routeName
     * @param array            $routesParams
     */
    public function breadcrumbFunction(\Twig_Environment $env, $routeName, $routesParams = array())
    {
        $items = $this->breadcrumbGenerator->getBreadcrumbItems($routeName, $routesParams);

        return $env->render('ServiceCiviqueSeoBundle::breadcrumb.html.twig', array(
            'items' => $items
        ));
    }

    public function getName()
    {
        return 'service_civique_seo_extension';
    }
}

<?php

namespace ServiceCivique\Bundle\SeoBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ServiceCivique\Bundle\SeoBundle\DependencyInjection\Compiler\OverrideTemplateEngine;

class ServiceCiviqueSeoBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new OverrideTemplateEngine()
        );
    }
}

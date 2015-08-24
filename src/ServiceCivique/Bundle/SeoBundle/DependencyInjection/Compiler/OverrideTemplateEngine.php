<?php

namespace ServiceCivique\Bundle\SeoBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

class OverrideTemplateEngine implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $templating = $container->findDefinition('templating');
        $container->setDefinition('base_templating', $templating);

        $container->removeDefinition('templating');

        $templating = new Definition(
            'ServiceCivique\Bundle\SeoBundle\Templating\ListenableEngine',
            array(new Reference('base_templating'))
        );

        $templating->addMethodCall('setContainer', array(new Reference('service_container')));

        $container->setDefinition('templating', $templating);
    }
}

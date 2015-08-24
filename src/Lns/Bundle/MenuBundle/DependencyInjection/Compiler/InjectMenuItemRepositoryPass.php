<?php

namespace Lns\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InjectMenuItemRepositoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('lns.repository.menu')->addMethodCall('setMenuItemRepository', array(
            new Reference('lns.repository.menu_item')
        ));
    }
}

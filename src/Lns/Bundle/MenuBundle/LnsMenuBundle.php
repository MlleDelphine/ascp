<?php

namespace Lns\Bundle\MenuBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Lns\Bundle\MenuBundle\DependencyInjection\Compiler\InjectMenuItemRepositoryPass;

class LnsMenuBundle extends Bundle
{
    /**
     * Return array of currently supported drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBundlePrefix()
    {
        return 'lns_menu';
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $interfaces = array(
            'Lns\Component\Menu\Model\MenuInterface'     => 'lns.model.menu.class',
            'Lns\Component\Menu\Model\MenuItemInterface' => 'lns.model.menu_item.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('lns_menu', $interfaces));

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'Lns\Component\Menu\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'lns_menu.driver.doctrine/orm'));
        $container->addCompilerPass(new InjectMenuItemRepositoryPass());
    }
}

<?php

namespace Lns\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('lns_menu');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('driver')->defaultValue('doctrine/orm')->end()
            ->end()
        ;

        $this->addCacheSection($rootNode);
        $this->addClassesSection($rootNode);
        $this->addValidationGroupsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `cache` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addCacheSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('service')->defaultValue('lns.menu.file_cache')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `validation_groups` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addValidationGroupsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('validation_groups')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('menu')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('lns'))
                        ->end()
                        ->arrayNode('menu_item')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('lns'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `classes` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('menu')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Lns\Component\Menu\Model\Menu')->end()
                                ->scalarNode('controller')->defaultValue('Lns\Bundle\MenuBundle\Controller\MenuController')->end()
                                ->scalarNode('repository')->defaultValue('Lns\Bundle\MenuBundle\Doctrine\ORM\MenuRepository')->end()
                                ->scalarNode('form')->defaultValue('Lns\Bundle\MenuBundle\Form\Type\MenuType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('menu_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Lns\Component\Menu\Model\MenuItem')->end()
                                ->scalarNode('controller')->defaultValue('Lns\Bundle\MenuBundle\Controller\MenuItemController')->end()
                                ->scalarNode('repository')->defaultValue('Lns\Bundle\MenuBundle\Doctrine\ORM\MenuItemRepository')->end()
                                ->scalarNode('form')->defaultValue('Lns\Bundle\MenuBundle\Form\Type\MenuItemType')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}

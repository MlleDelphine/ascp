<?php

namespace Lns\Bundle\MenuBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

class LnsMenuExtension extends AbstractResourceExtension
{
    protected $applicationName = 'lns';

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS);
    }

    protected function process(array $config, ContainerBuilder $container)
    {
        $cacheProvider = new DefinitionDecorator('lns.menu.cache.abstract.doctrine_cache_adapter');
        $cacheProvider
            ->replaceArgument(1, new Reference($config['cache']['service']))
        ;

        $container->setDefinition('lns.menu.cache', $cacheProvider);

        return $config;
    }
}

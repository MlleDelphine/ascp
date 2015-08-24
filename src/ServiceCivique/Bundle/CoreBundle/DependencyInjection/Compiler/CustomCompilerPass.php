<?php

namespace ServiceCivique\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CustomCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->alterMissionSearchRepositoryDefinition($container);
    }

    /**
     * alterMissionSearchRepositoryDefinition
     *
     * @param ContainerBuilder $container
     */
    protected function alterMissionSearchRepositoryDefinition(ContainerBuilder $container)
    {
        $mission_search              = $container->getDefinition('service_civique.repository.mission_search');
        $polem_departements_provider = $container->getDefinition('polem_departements.provider');

        $mission_search->addMethodCall('setDepartementProvider', array($polem_departements_provider));
    }
}

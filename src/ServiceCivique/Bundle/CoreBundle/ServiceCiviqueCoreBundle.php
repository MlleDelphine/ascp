<?php

namespace ServiceCivique\Bundle\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use ServiceCivique\Bundle\CoreBundle\DependencyInjection\Compiler\CustomCompilerPass;

class ServiceCiviqueCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CustomCompilerPass($container));
    }
}

<?php

namespace ServiceCivique\Bundle\MenuBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ServiceCiviqueMenuBundle extends Bundle
{
    public function getParent()
    {
        return 'LnsMenuBundle';
    }
}

<?php

namespace ServiceCivique\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ServiceCiviqueUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}

<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\Configuration;

class ConfigurationManipulator
{
    protected $config;
    protected $reflexionProperty;

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;

        $reflexionClass = new \ReflectionClass($this->config);
        $reflexionProperty = $reflexionClass->getProperty('parameters');
        $reflexionProperty->setAccessible(true);
        $this->reflexionProperty = $reflexionProperty;
    }

    public function set($key, $value)
    {
        $parameters = $this->reflexionProperty->getValue($this->config);
        $parameters[$key] = $value;
        $parameters = $this->reflexionProperty->setValue($this->config, $parameters);

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }
}

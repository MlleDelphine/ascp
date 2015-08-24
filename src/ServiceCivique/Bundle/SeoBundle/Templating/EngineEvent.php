<?php

namespace ServiceCivique\Bundle\SeoBundle\Templating;

use Symfony\Component\EventDispatcher\Event;

class EngineEvent extends Event
{
    protected $parameters = [];

    /**
     * __construct
     *
     * @param mixed $name
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * Get parameters.
     *
     * @return parameters.
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}

<?php

namespace ServiceCivique\Bundle\SeoBundle\Templating;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Decorate base template engine
 *
 * @see EngineInterface
 */
class ListenableEngine implements EngineInterface, ContainerAwareInterface
{
    protected $firstCall = true;

    /**
     * @param EngineInterface $baseEngine
     */
    public function __construct(EngineInterface $baseEngine)
    {
        $this->baseEngine = $baseEngine;
    }

    /**
     * {@inheritDoc}
     */
    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        $this->dispatch($parameters);

        return $this->baseEngine->renderResponse($view, $parameters, $response);
    }

    /**
     * {@inheritDoc}
     */
    public function render($name, array $parameters = array())
    {
        $this->dispatch($parameters);

        return $this->baseEngine->render($name, $parameters);
    }

    protected function dispatch($parameters)
    {
        if ($this->firstCall) {
            $event = new EngineEvent($parameters);
            $this->getEventDispatcher()->dispatch(EngineEvents::PRE_RENDER, $event);
            $this->firstCall = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function exists($name)
    {
        return $this->baseEngine->exists($name);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($name)
    {
        return $this->baseEngine->supports($name);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getRequest()
    {
        return $this->container->get('request');
    }

    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

}

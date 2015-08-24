<?php

namespace ServiceCivique\Bundle\SeoBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use ServiceCivique\Bundle\SeoBundle\Templating\EngineEvent;
use ServiceCivique\Bundle\SeoBundle\Templating\EngineEvents;
use Symfony\Component\HttpKernel\KernelEvents;

class SeoPageConfigurator implements EventSubscriberInterface
{
    private $translator;
    private $seoPage;
    private $routeName;
    private $parameters = array();
    private $title;
    private $config;

    /**
     * __construct
     *
     * @param SeoPageInterface $seoPage
     * @param Translator       $translator
     */
    public function __construct(SeoPageInterface $seoPage, Translator $translator)
    {
        $this->seoPage    = $seoPage;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            EngineEvents::PRE_RENDER => 'onTemplateEnginePreRender',
            KernelEvents::CONTROLLER => 'onKernelController'
        );
    }

    /**
     * onKernelController
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $this->routeName = $event->getRequest()->get('_route');
    }

    /**
     * onTemplateEnginePreRender
     *
     * @param EngineEvent $event
     */
    public function onTemplateEnginePreRender(EngineEvent $event)
    {
        if (!isset($this->config[$this->routeName])) {
            return;
        }

        $config = $this->config[$this->routeName];
        $title = $this->translator->trans($config['title'], $this->parameters, 'seo');
        $this->seoPage->addTitle($title);

        if (isset($config['metas'])) {
            foreach ($config['metas'] as $id => $values) {
                foreach ($values as $key => $value) {
                    $this->seoPage->addMeta($id, $key, $this->translator->trans($value, $this->parameters, 'seo'));
                }
            }
        }
    }

    /**
     * setParameter
     *
     * @param string $key
     * @param string $value
     */
    public function setParameter($key, $value)
    {
        $this->parameters['%' . $key . '%'] = $value;

        return $this;
    }

    /**
     * Get title.
     *
     * @return title.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param title the value to set.
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get config.
     *
     * @return config.
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set config.
     *
     * @param config the value to set.
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }
}

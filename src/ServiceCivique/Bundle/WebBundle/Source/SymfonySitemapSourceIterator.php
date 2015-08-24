<?php

namespace ServiceCivique\Bundle\WebBundle\Source;

use Symfony\Component\Routing\RouterInterface;
use Exporter\Source\SourceIteratorInterface;

class SymfonySitemapSourceIterator implements SourceIteratorInterface
{
    protected $source;

    protected $parameters;

    /**
     * @param SourceIteratorInterface $source
     * @param RouterInterface         $router
     */
    public function __construct(SourceIteratorInterface $source, RouterInterface $router)
    {
        $this->source = $source;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $data = $this->source->current();

        $parameters = isset($data['parameters']) ? $data['parameters'] : array();

        if (!isset($data['url'])) {
            $data['url'] = $this->router->generate($data['route_name'], $parameters, true);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->source->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->source->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->source->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->source->rewind();
    }
}

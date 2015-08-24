<?php

namespace Lns\Component\Menu\Factory;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class BaseUrlExtension implements ExtensionInterface
{
    protected $requestStack;

    /**
     * __construct
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function buildOptions(array $options)
    {
        if (!empty($options['uri']) && substr($options['uri'], 0, 1) == '/') {
            $baseUri = $this->requestStack->getCurrentRequest()->getBaseUrl();

            if (empty($baseUri)) {
                return $options;
            }

            if (0 !== strpos($options['uri'], $baseUri)) {
                $options['uri'] = $baseUri . $options['uri'];
            }
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function buildItem(ItemInterface $item, array $options)
    {
    }
}

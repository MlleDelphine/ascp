<?php

namespace Lns\Component\Menu\Matcher\Voter;

use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;

class RequestVoter implements VoterInterface
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

    public function matchItem(ItemInterface $item)
    {
        $uri = $this->requestStack->getCurrentRequest()->getRequestUri();

        if (null === $uri || null === $item->getUri()) {
            return null;
        }

        if ($this->compare($item->getUri(), $uri)) {
            return true;
        }

        return null;
    }

    protected function clearUri($uri)
    {
        return rtrim($uri, '/');
    }

    protected function compare($uri1, $uri2)
    {
        return $this->clearUri($uri1) === $this->clearUri($uri2);
    }
}

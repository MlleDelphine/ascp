<?php

namespace Lns\Component\Menu\Cache;

use Knp\Menu\Loader\LoaderInterface;
use Knp\Menu\ItemInterface;
use Doctrine\Common\Cache\Cache;

class DoctrineCache extends AbstractCache
{
    /**
     * cache
     *
     * @var Cache
     */
    protected $cache;

    public function __construct(LoaderInterface $loader, Cache $cache)
    {
        parent::__construct($loader);
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function save($id, ItemInterface $menu)
    {
        $data = $this->transformItemToArray($menu);

        return $this->cache->save($id, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function fetch($id)
    {
        $data = $this->cache->fetch($id);

        return $this->transformArrayToItem($data);
    }

    /**
     * {@inheritDoc}
     */
    public function contains($id)
    {
        return $this->cache->contains($id);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id)
    {
        return $this->cache->delete($id);
    }
}

<?php

namespace Lns\Component\Menu\Provider;

use Knp\Menu\Provider\MenuProviderInterface;
use Lns\Component\Menu\Cache\CacheInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ProxyCacheProvider implements MenuProviderInterface
{
    /**
     * realProvider
     *
     * @var MenuProviderInterface
     */
    protected $realProvider;

    /**
     * cache
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * menuRepository
     *
     * @var EntityRepository
     */
    protected $menuRepository;

    /**
     * __construct
     *
     * @param MenuProviderInterface $realProvider
     * @param CacheInterface        $cache
     * @param EntityRepository      $menuRepository
     */
    public function __construct(
        MenuProviderInterface $realProvider,
        CacheInterface $cache,
        EntityRepository $menuRepository
    )
    {
        $this->realProvider   = $realProvider;
        $this->cache          = $cache;
        $this->menuRepository = $menuRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function get($name, array $options = array())
    {
        $cacheId = $this->getCacheId($name);

        if ($this->cache->contains($cacheId)) {
            return $this->cache->fetch($cacheId);
        }

        $menu = $this->realProvider->get($name, $options);

        $this->cache->save($cacheId, $menu);

        return $menu;
    }

    /**
     * {@inheritDoc}
     */
    public function has($name, array $options = array())
    {
        $cacheId = $this->getCacheId($name);
        if ($this->cache->contains($cacheId)) {
            return true;
        }

        return $this->realProvider->has($cacheId, $options);
    }

    public function getCacheId($name)
    {
        if ($menu = $this->getMenuBySlug($name)) {
            return $menu->getId() . '_' . $menu->getUpdated()->format('U');
        }

        return 'default';
    }

    private function getMenuBySlug($name)
    {
        $query = $this->menuRepository->createQueryBuilder('m')
            ->where('m.slug = :slug')
            ->setMaxResults(1)
            ->setParameter(':slug', $name)
            ->getQuery();

        $query->useResultCache(true, 60, sprintf('lns_menu.%s', $name));

        return $query->getOneOrNullResult();
    }
}

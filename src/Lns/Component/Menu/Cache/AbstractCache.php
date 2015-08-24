<?php

namespace Lns\Component\Menu\Cache;

use Knp\Menu\Loader\LoaderInterface;
use Knp\Menu\Util\MenuManipulator;
use Knp\Menu\ItemInterface;

abstract class AbstractCache implements CacheInterface
{
    /**
     * loader
     *
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * menuManipulator
     *
     * @var MenuManipulator
     */
    protected $menuManipulator;

    /**
     * __construct
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader          = $loader;
        $this->menuManipulator = new MenuManipulator();
    }

    /**
     * transformItemToArray
     *
     * @param  ItemInterface $item
     * @return array
     */
    protected function transformItemToArray(ItemInterface $item)
    {
        return $this->menuManipulator->toArray($item);
    }

    /**
     * transformArrayToItem
     *
     * @param  array         $array
     * @return ItemInterface $item
     */
    protected function transformArrayToItem(array $array)
    {
        return $this->loader->load($array);
    }
}

<?php

namespace Lns\Bundle\MenuBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Lns\Component\Menu\Cache\CacheInterface;
use Lns\Component\Menu\Model\MenuInterface;

class CacheInvalider implements EventSubscriberInterface
{
    protected $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'lns.menu.post_delete'      => 'onMenuChange',
            'lns.menu.post_update'      => 'onMenuChange',
            'lns.menu_item.post_update' => 'onMenuItemChange',
            'lns.menu_item.post_delete' => 'onMenuItemChange',
            'lns.menu_item.post_create' => 'onMenuItemChange'
        ];
    }

    public function onMenuChange(GenericEvent $event)
    {
        $this->clearMenuCache($event->getSubject());
    }

    public function onMenuItemChange(GenericEvent $event)
    {
        $this->clearMenuCache($event->getSubject()->getMenu());
    }

    protected function clearMenuCache(MenuInterface $menu)
    {
        $this->cache->delete($menu->getSlug());
    }
}

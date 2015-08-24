<?php

namespace Lns\Bundle\MenuBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Lns\Component\Menu\Model\MenuInterface;
use Doctrine\ORM\EntityManager;

class MenuUpdater implements EventSubscriberInterface
{
    protected $EntityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'lns.menu_item.post_update' => 'onMenuItemChange',
            'lns.menu_item.post_delete' => 'onMenuItemChange',
            'lns.menu_item.post_create' => 'onMenuItemChange'
        ];
    }

    public function onMenuItemChange(GenericEvent $event)
    {
        $this->updateMenu($event->getSubject()->getMenu());
    }

    protected function updateMenu(MenuInterface $menu)
    {
        $menu->setUpdated(new \Datetime());
        $this->em->persist($menu);
        $this->em->flush($menu);
    }
}

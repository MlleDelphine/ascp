<?php

namespace Lns\Bundle\MenuBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class MenuRepository extends EntityRepository
{
    protected $menuItemRepository;

    public function createNew()
    {
        $menu = parent::createNew();

        if ($this->menuItemRepository) {
            $rootMenuItem = $this->menuItemRepository->createNew();
            $rootMenuItem
                ->setName('root')
                ->setMenu($menu);

            $this->getEntityManager()->persist($rootMenuItem);
            $menu->setRootMenuItem($rootMenuItem);
        }

        return $menu;
    }

    public function findOneByUri($uri)
    {
        $query = $this->getQueryBuilder()
            ->join($this->getAlias() . '.items', 'i')
            ->where('i.url = :url')
            ->setMaxResults(1)
            ->setParameter(':url', $uri)
            ->getQuery();

        $query->useResultCache(true, 500, sprintf('lns_menu.%s', md5($uri)));

        return $query->getOneOrNullResult();
    }

    public function setMenuItemRepository($menuItemRepository)
    {
        $this->menuItemRepository = $menuItemRepository;
    }
}

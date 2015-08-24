<?php

namespace Lns\Bundle\MenuBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MenuItemController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        if (null === $menuId = $this->getRequest()->get('menuId')) {
            throw new NotFoundHttpException('No menu id given.');
        }
        if (!$menu = $this->getMenuRepository()->find($menuId)) {
            throw new NotFoundHttpException('Requested menu does not exist.');
        }
        $menuItem = parent::createNew();
        $menuItem->setMenu($menu);

        $menuItem->setParent($menu->getRootMenuItem());

        return $menuItem;
    }

    /**
     * Get menu repository.
     *
     * @return ObjectRepository
     */
    protected function getMenuRepository()
    {
        return $this->get('lns.repository.menu');
    }
}

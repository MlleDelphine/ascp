<?php

namespace Lns\Bundle\MenuBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Event\ResourceEvent;

class MenuController extends ResourceController
{
    public function manageAction(Request $request)
    {
        // retreive current menu and menu items
        $menu = $this->findOr404($request);

        // retreive menu items
        $menuItems = $this->getMenuItems($menu, false);

        $view = $this
            ->view()
            ->setTemplate('LnsMenuBundle:Backend\MenuItem:menu_tree.html.twig')
            ->setData([
                'menu_items' => $menuItems,
                'menu'       => $menu
            ])
        ;

        return $this->handleView($view);
    }

    public function updateTreeAction(Request $request)
    {
        $menu = $this->findOr404($request);

        $menuItems = $this->getMenuItems($menu, true);

        $menuItemRepository = $this->getMenuItemRepository();

        $em = $this->get('doctrine.orm.entity_manager');

        foreach ($request->request->get('links') as $link) {

            if ($parentId = intval($link['parent'])) {
                $parent = $menuItems[$parentId];
                $menuItem = $menuItems[$link['id']];

                $menuItem->setParent($parent);
                $menuItem->setWeight($link['weight']);

                $em->persist($menuItem);
            }
        }

        $em->flush();
        $em->clear();

        $this->get('event_dispatcher')->dispatch('lns.menu.post_update', new ResourceEvent($menu));

        $menuItemRepository->reorder($menu->getRootMenuItem(), 'weight');

        return $this->redirectHandler->redirectToReferer();
    }

    protected function getMenuItems($menu, $include_root = false)
    {
        return $this->getMenuItemRepository()
            ->findMenuItemsByMenu($menu, $include_root);
    }

    protected function getMenuItemRepository()
    {
         return $this->container
            ->get('lns.repository.menu_item');
    }
}

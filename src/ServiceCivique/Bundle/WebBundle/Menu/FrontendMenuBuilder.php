<?php

namespace ServiceCivique\Bundle\WebBundle\Menu;

use Symfony\Component\HttpFoundation\Request;
use ServiceCivique\Bundle\MenuBundle\ContextResolver;
use Symfony\Component\Security\Core\SecurityContextInterface;
use ServiceCivique\Bundle\CoreBundle\Entity\MenuItem;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class FrontendMenuBuilder
 */
class FrontendMenuBuilder extends MenuBuilder
{
    public function createTopMenu(Request $request, ContextResolver $contextResolver, SecurityContextInterface $securityContext)
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav pull-right');
        $menu->setChildrenAttribute('id', 'top-menu');

        // // set first item : connexion or panel links
        // if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_ORGANIZATION')) {
        //     $user = $this->securityContext->getToken()->getUser();

        //     $name = $user->getOrganization() ? $user->getOrganization()->getName() : $user->getUsername();

        //     $menu->addChild($name, array(
        //         'uri' => '#organization-panel',
        //         'linkAttributes' => array(
        //             'aria-controls' => 'organization-pannel',
        //             'aria-expanded' => 'false',
        //             'role'          => 'button',
        //             'class'         => 'link-user',
        //         )
        //     ));
        // } elseif ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_USER')) {
        //     $user = $this->securityContext->getToken()->getUser();
        //     $firstname = $user->getFirstname();
        //     if ($this->securityContext->isGranted('ROLE_WEBMASTER')) {
        //         $firstname = 'Administrateur';
        //     }

        //     $connexionMenu = $this->factory->createItem($firstname, array(
        //         'uri' => '#user-panel',
        //         'linkAttributes' => array(
        //             'aria-controls' => 'user-pannel',
        //             'aria-expanded' => 'false',
        //             'role'          => 'button'
        //         )
        //     ));
        //     $menu->addChild($connexionMenu);
        //     $menu->setCurrent(false);
        // } else {
        //     $connexionMenu = $this->factory->createItem('S’identifier', array(
        //         'uri' => '#login-panel',
        //         'linkAttributes' => array(
        //             'id'            => 'connexion',
        //             'aria-controls' => 'login-pannel',
        //             'aria-expanded' => 'false',
        //             'role'          => 'button'
        //         )
        //     ));
        //     $menu->addChild($connexionMenu);
        //     $menu->setCurrent(false);
        // }

        // get current context
        $currentContext = $contextResolver->getContext();

        // create other items
        foreach (['corporate', 'organization', 'jeunes', 'faqs'] as $subMenuSlug) {
            $firstChild = $this->menuProvider
                ->get($subMenuSlug)
                ->getFirstChild();

            if (!$firstChild) {
                continue;
            }

            // copy first child without children
            $subMenu = $firstChild
                ->setChildren([])
                ->copy();

            if ($subMenuSlug == $currentContext) {
                $subMenu->setCurrent(true);
            }

            $menu->addChild($subMenu);
        }

        return $menu;
    }

    public function createSecondaryMenu(Request $request, ContextResolver $contextResolver)
    {
        $currentContext = $contextResolver->getContext();

        $menu = $this->menuProvider
            ->get($currentContext)
            ->getFirstChild();

        // if menu is empty create new item
        if (!$menu) {
            $menu = $this->factory->createItem('secondary');
        }

        return $menu;
    }

    /**
     * Build frontend sidebar menu
     *
     * @param Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createSidebarMenu(Request $request, ContextResolver $contextResolver)
    {
        $secondaryMenu = $this->createSecondaryMenu($request, $contextResolver);

        $currentItem = $this->getCurrentMenuItem($secondaryMenu);

        if (!$currentItem) {
            return $this->factory->createItem('sidebar');
        }

        $menuRoot = $this->getAncestor($currentItem, $currentItem->getLevel() - 2);
        //$menuRoot = $currentItem->getParent()->getParent();

        $menuRoot->setChildrenAttribute('class', 'nav');
        $menuRoot->setChildrenAttribute('id', 'sidebar-menu');

        return $menuRoot;
    }

    public function getAncestor($menuItem, $depth)
    {
        if ($depth === 0) {
            return $menuItem;
        }

        return $this->getAncestor($menuItem->getParent(), $depth - 1);
    }

    /**
     * Build frontend footer
     *
     * @param Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createFooterMenuCol1(Request $request)
    {
        $menu = $this->factory->createItem('footer-col1');

        $menu->setChildrenAttribute('class', 'nav');
        $menu->setChildrenAttribute('id', 'footer-menu');

        $jeunesMenu = $menu->addChild('Jeunes', array('uri' => '/'));

        $menu->addChild('Organismes', array('uri' => '/organismes/'));
        $menu->addChild('À propos de nous', array('uri' => '/a-propos-de-nous/'));

        return $menu;
    }

    /**
     * Build frontend footer
     *
     * @param Request         $request
     * @param RouterInterface $router
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createFooterMenuCol2(Request $request, RouterInterface $router)
    {
        $menu = $this->menuProvider->get('footer');

        $menu->setChildrenAttribute('class', 'nav');
        $menu->setChildrenAttribute('id', 'footer-menu-2');

        return $menu;
    }

    /**
     * Build frontend footer-rs
     *
     * @param Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createFooterRsMenu(Request $request)
    {
        $menu = $this->factory->createItem('footer-rs');

        $menu->setChildrenAttribute('class', '');
        $menu->setChildrenAttribute('id', 'footer-menu-rs');

        $menu->addChild('Twitter', array(
            'uri' => 'https://twitter.com/servicecivique',
            'linkAttributes' => array(
                'id' => $this->translate('rs-twitter'),
                'target' => '_blank',
                'title'  => 'Twitter (s\'ouvre dans une nouvelle fenêtre)'
            )
        ));

        $menu->addChild('Facebook', array(
            'uri'            => 'https://www.facebook.com/service-civique',
            'linkAttributes' => array(
                'target' => '_blank',
                'id'     => 'rs-fb',
                'title'  => 'Facebook (s\'ouvre dans une nouvelle fenêtre)'
            )
        ));

        $menu->addChild('Tumblr', array(
            'uri'            => 'http://servicecivique.tumblr.com',
            'linkAttributes' => array(
                'target' => '_blank',
                'id'     => 'rs-tumblr',
                'title'  => 'Tumblr (s\'ouvre dans une nouvelle fenêtre)'
            )
        ));

        $menu->addChild('Dailymotion', array(
            'uri'            => 'http://www.dailymotion.com/service-civique',
            'linkAttributes' => array(
                'target' => '_blank',
                'id'     => 'rs-dailymotion',
                'title'  => 'Dailymotion (s\'ouvre dans une nouvelle fenêtre)'
            )
        ));

        $menu->addChild('Youtube', array(
            'uri'            => 'https://www.youtube.com/user/leservicecivique',
            'linkAttributes' => array(
                'target' => '_blank',
                'id'     => 'rs-youtube',
                'title'  => 'Youtube (s\'ouvre dans une nouvelle fenêtre)'
            )
        ));

        $menu->addChild('Vimeo', array(
            'uri'            => 'http://vimeo.com/servicecivique',
            'linkAttributes' => array(
                'target' => '_blank',
                'id'     => 'rs-vimeo',
                'title'  => 'Vimeo (s\'ouvre dans une nouvelle fenêtre)'
            )
        ));

        $menu->addChild('RSS', array(
            'uri'            => '/actualites',
            'linkAttributes' => array(
                'id' => 'rs-rss'
            )
        ));

        $menu->addChild('Newsletter', array(
            'uri'            => '/newsletter',
            'linkAttributes' => array(
                'id' => 'rs-newsletter'
            )
        ));

        return $menu;
    }

    /**
     * Build frontend footer
     *
     * @param Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createFooterBottomMenu(Request $request)
    {
        $menu = $this->factory->createItem('footer-bottom');

        $menu->setChildrenAttribute('class', 'nav');
        $menu->setChildrenAttribute('id', 'footer-bottom-menu');

        $menu->addChild('France.fr', array(
            'uri'            => 'http://www.france.fr',
            'linkAttributes' => array(
                'target' => '_blank',
                'title'  => 'France.fr (s\'ouvre dans une nouvelle fenêtre)'
            )
        ));

        $menu->addChild('Gouvernement.fr', array(
            'uri'            => 'http://www.gouvernement.fr',
            'linkAttributes' => array(
                'target' => '_blank',
                'title'  => 'Gouvernement.fr (s\'ouvre dans une nouvelle fenêtre)'
            )
        ));
        $menu->addChild('legifrance.gouv.fr', array(
            'uri'            => 'http://www.legifrance.gouv.fr',
            'linkAttributes' => array(
                'target' => '_blank',
                'title'  => 'Legifrance.gouv.fr (s\'ouvre dans une nouvelle fenêtre)'
            )
        ));
        $menu->addChild('Service-Public.fr', array(
            'uri'            => 'http://www.service-public.fr',
            'linkAttributes' => array(
                'target' => '_blank',
                'title'  => 'Service-Public.fr (s\'ouvre dans une nouvelle fenêtre)'
            )
        ));

        return $menu;
    }
}

<?php

namespace Lns\Bundle\MenuBundle\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Gedmo\Tree\RepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;

class MenuProvider implements MenuProviderInterface
{
    /**
     * @var FactoryInterface
     */
    protected $factory = null;

    /**
     * @var ObjectRepository
     */
    protected $menuRepository = null;

    /**
     * @var RepositoryInterface
     */
    protected $menuItemRepository = null;

    /**
     * @param FactoryInterface    $factory            the menu factory used to create the menu item
     * @param ObjectRepository    $menuRepository
     * @param RepositoryInterface $menuItemRepository
     */
    public function __construct(FactoryInterface $factory, ObjectRepository $menuRepository, RepositoryInterface $menuItemRepository)
    {
        $this->factory            = $factory;
        $this->menuItemRepository = $menuItemRepository;
        $this->menuRepository     = $menuRepository;
    }

    /**
     * Retrieves a menu by its name
     *
     * @param  string                    $name
     * @param  array                     $options
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException if the menu does not exists
     */
    public function get($name, array $options = array())
    {
        $childrenHierarchy = [];

        if ($menuObject = $this->menuRepository->findOneBySlug($name)) {

            $this->menuItemRepository->setChildrenIndex('children');

            $childrenHierarchy = $this->menuItemRepository->childrenHierarchy(
                $menuObject->getRootMenuItem(),
                false,
                [],
                false
            );
        }

        $menu = $this->createMenuFromChildrenHierarchy($childrenHierarchy);

        return $menu;
    }

    /**
     * Checks whether a menu exists in this provider
     *
     * @param  string $name
     * @param  array  $options
     * @return bool
     */
    public function has($name, array $options = array())
    {
        $menu = $this->menuRepository->findOneBySlug($name);

        return (bool) $menu;
    }

    protected function createMenuFromChildrenHierarchy(array $hierarchy, $name = 'root')
    {
        $menu = $this->factory->createItem($name);

        foreach ($hierarchy as $node) {
            $menu->addChild($this->createItemFromArrayNode($node));
        }

        return $menu;
    }

    /**
     * createItemFromArrayNode
     *
     * @param array  $tree
     * @param string $name
     */
    protected function createItemFromArrayNode(array $node, $name = null)
    {
        list($name, $options, $children) = $this->processArrayNode($node, $name);
        $item = $this->factory->createItem($name, $options);

        foreach ($children as $name => $child) {
            $item->addChild($this->createItemFromArrayNode($child, $name));
        }

        return $item;
    }

    /**
     * processArrayNode
     *
     * @param array  $node
     * @param string $name
     */
    protected function processArrayNode(array $node, $name)
    {
        $options = isset($node['options']) ? $node['options'] : [];
        $options['uri'] = isset($node['url']) ? $node['url'] : null;

        return [
            isset($node['name']) ? $node['name'] : $name,
            $options,
            isset($node['children']) ? $node['children'] : []
        ];
    }
}

# Lns menu Bundle

## Installation

@TODO

## Config

```
lns_menu:
    driver:               doctrine/orm
    cache:
        service:              lns.menu.file_cache #doctrine cache service
    classes:
        menu:
            model:                Lns\Component\Menu\Model\Menu
            controller:           Lns\Bundle\MenuBundle\Controller\MenuController
            repository:           Lns\Bundle\MenuBundle\Doctrine\ORM\MenuRepository
            form:                 Lns\Bundle\MenuBundle\Form\Type\MenuType
        menu_item:
            model:                Lns\Component\Menu\Model\MenuItem
            controller:           Lns\Bundle\MenuBundle\Controller\MenuItemController
            repository:           Lns\Bundle\MenuBundle\Doctrine\ORM\MenuItemRepository
            form:                 Lns\Bundle\MenuBundle\Form\Type\MenuItemType
    validation_groups:
        menu:

            # Default:
            - lns
        menu_item:

            # Default:
            - lns
```

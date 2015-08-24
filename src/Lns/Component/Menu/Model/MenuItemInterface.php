<?php

namespace Lns\Component\Menu\Model;

use Knp\Menu\NodeInterface;

interface MenuItemInterface extends NodeInterface
{
    public function setName($name);
    public function setUrl($url);
}

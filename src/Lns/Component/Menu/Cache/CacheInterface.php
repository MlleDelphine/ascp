<?php

namespace Lns\Component\Menu\Cache;

use Knp\Menu\ItemInterface;

interface CacheInterface
{
    /**
     * save
     *
     * @param string        $id
     * @param ItemInterface $menu
     */
    public function save($id, ItemInterface $menu);

    /**
     * fetch
     *
     * @param  string  $id
     * @return boolean
     */
    public function fetch($id);

    /**
     * contains
     *
     * @param  string  $id
     * @return boolean
     */
    public function contains($id);

    /**
     * delete
     *
     * @param  string  $id
     * @return boolean
     */
    public function delete($id);
}

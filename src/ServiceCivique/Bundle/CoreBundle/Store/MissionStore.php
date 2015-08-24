<?php

namespace ServiceCivique\Bundle\CoreBundle\Store;

class MissionStore
{
    protected $store;

    public function __construct($store)
    {
        $this->store = $store;
    }

    /**
     * add
     *
     * @param mixed $data
     */
    public function add($data)
    {
        $prefix = 'preview_mission_';

        $key = uniqid($prefix, true);

        $this->store->set(
            $key,
            serialize($data)
        );

        return $key;
    }

    /**
     * get
     *
     * @param mixed $key
     * @param mixed $resource
     */
    public function get($key, $form, $remove = true)
    {
        $data = $this->store->get($key);

        if (!$data) {
            return null;
        }

        $value = stream_get_contents($data->getDataValue());
        $form->submit(unserialize($value));

        if ($remove) {
            $this->store->remove($key);
        }

        return $form->getData();
    }

    /**
     * deleteFromStore
     *
     * @param mixed $key
     */
    public function remove($key)
    {
        $this->store->remove($key);
    }
}

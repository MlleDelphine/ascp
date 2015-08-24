<?php

namespace ServiceCivique\Bundle\KeyValueStoreBundle;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Persistence\ObjectManager;

use ServiceCivique\Bundle\KeyValueStoreBundle\Entity\KeyValue;

class KeyValueStore
{
    protected $repository;
    protected $objectManager;

    /**
     * __construct
     *
     * @param ObjectRepository $repository
     * @param ObjectManager    $objectManager
     */
    public function __construct(ObjectRepository $repository, ObjectManager $objectManager)
    {
        $this->repository    = $repository;
        $this->objectManager = $objectManager;
    }

    /**
     * set
     *
     * @param string $key
     * @param string $value
     */
    public function set($key, $value)
    {
        $keyValue = new KeyValue();
        $keyValue->setDataKey($key);
        $keyValue->setDataValue($value);
        $keyValue->setCreated(new \DateTime());

        $this->objectManager->persist($keyValue);
        $this->objectManager->flush($keyValue);
    }

    /**
     * get
     *
     * @param mixed $key
     */
    public function get($key)
    {
        return $this->repository->findOneByDataKey($key);
    }

    /**
     * remove
     *
     * @param string $key
     */
    public function remove($key)
    {

        $data = $this->get($key);

        if ($data) {
            $this->objectManager->remove($data);
            $this->objectManager->flush($data);
        }
    }
}

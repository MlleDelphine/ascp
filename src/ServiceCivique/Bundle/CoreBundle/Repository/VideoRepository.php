<?php

namespace ServiceCivique\Bundle\CoreBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class VideoRepository extends EntityRepository
{
    public function findLast()
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.published', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * findAllOrderedBy
     *
     * @param array $order
     */
    public function findAllOrderedBy($order)
    {
        return $this->createQueryBuilder('v')
            ->orderBy(sprintf('v.%s', key($order)), current($order))
            ->getQuery()
            ->getResult();
    }

}

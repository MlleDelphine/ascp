<?php

namespace ServiceCivique\Bundle\CoreBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ProfileRepository extends EntityRepository
{

    public function getAllWithCV($offset = 0, $max = 100)
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $queryBuilder
            ->where('p.cv IS NOT null')
            ->setMaxResults($max)
            ->setFirstResult($offset)
        ;

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;

    }

    public function getAllWithCVCount()
    {
        return $this->createQueryBuilder('p')
                    ->select('COUNT(p)')
                    ->where('p.cv IS NOT null')
            ->getQuery()->getSingleScalarResult()
        ;
    }

}

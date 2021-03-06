<?php

namespace ServiceCivique\Bundle\CoreBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * PartnerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PartnerRepository extends EntityRepository
{
     public function findLast()
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->orderBy('p.published', 'DESC')
            ->setMaxResults(1);
        ;

        return $qb->getQuery()->getSingleResult();
    }
}

<?php

namespace ServiceCivique\Bundle\CoreBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * MajorProgramRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MajorProgramRepository extends EntityRepository
{
    public function getMajorProgramsCount()
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->select('COUNT(o)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMajorProgramsByPosition($id)
    {
        $queryBuilder = $this->getQueryBuilder();

        $qb = $queryBuilder->orderBy('o.position', 'ASC');

        if ($id) {
            $qb
                ->where('o.id <> :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getResult();
    }

    public function getMajorProgramsAfterPosition($position)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->where('o.position > :position')
            ->setParameter('position', $position)
            ->getQuery()
            ->getResult();
    }
}
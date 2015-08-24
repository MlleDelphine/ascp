<?php

namespace ServiceCivique\Bundle\CoreBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class MenuItemRepository extends EntityRepository
{

    /**
     * @param array   $criteria
     * @param array   $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $orderBy);

        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if (null !== $offset) {
            $queryBuilder->setFirstResult($offset);
        }

        $query = $queryBuilder->getQuery();

        $cache_key = sprintf('menu_%s_s', md5(json_encode($criteria)), isset($criteria['parent'])  ? $criteria['parent'] : 'root');

        $query->useResultCache(true, 3600, $cache_key);

        return $query->getResult();
    }

}

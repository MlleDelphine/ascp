<?php

namespace ServiceCivique\Bundle\CoreBundle\SearchRepository;

use FOS\ElasticaBundle\Repository;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Elastica\Query\MatchAll;

class ApprovalRepository extends Repository implements RepositoryInterface
{

    public function findFromFrontend($criteria = null, $orderBy = null)
    {
        $query = $this->createQuery($criteria);

        if (!$orderBy) {
            $orderBy = array('organization_name' => 'asc');
        }

        $query = \Elastica\Query::create($query);
        $query->addSort(array(key($orderBy) => array('order' => $orderBy[key($orderBy)])));

        // build $query with Elastica objects
        return $this->findPaginated($query);
    }

    protected function createQuery($criteria)
    {
        $organization = (isset($criteria['organization_name']) && $criteria['organization_name'] != '') ? $criteria['organization_name'] : null;
        $approvalNumber = (isset($criteria['approval_number']) && $criteria['approval_number'] != '') ? $criteria['approval_number'] : null;

        if (!$organization && !$approvalNumber) {
            return new MatchAll();
        }

        $boolQuery = new \Elastica\Query\Bool();

        if ($organization) {
            $match = new \Elastica\Query\Match();
            $match->setFieldQuery('organization_name', $organization);

            $boolQuery->addMust($match);
        }

        if ($approvalNumber) {
            $match = new \Elastica\Query\Match();
            $match->setFieldQuery('approval_number', $approvalNumber);

            $boolQuery->addMust($match);
        }

        return $boolQuery;
    }

    public function createNew()
    {
        $className = $this->getClassName();

        return new $className();
    }

    public function createPaginator(array $criteria = null, array $orderBy = null)
    {
        return null;
    }

    public function findAll() {}

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {}

    public function findOneBy(array $criteria) {}

    public function getClassName() {}
}

<?php

namespace ServiceCivique\Bundle\CoreBundle\SearchRepository;

use FOS\ElasticaBundle\Repository;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Elastica\Query\MatchAll;
use Elastica\Query\QueryString;
use Elastica\Aggregation\Sum;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;

class MissionRepository extends Repository implements RepositoryInterface
{
    public function findFromOrganization($organization, $criteria = null, $orderBy = null)
    {
        $query = $this->createQuery($criteria);

        // Then we create filters depending on the chosen criterias
        $queryFilter = $this->createQueryFilter($criteria);
        $queryFilter = $this->applyOrganizationCriteria($queryFilter, $organization);

        if (!$orderBy) {
            $orderBy = array('published' => 'desc');
        }

        $filteredQuery = new \Elastica\Query\Filtered($query, $queryFilter);

        $query = \Elastica\Query::create($filteredQuery);
        $query->addSort(array(key($orderBy) => array('order' => $orderBy[key($orderBy)])));

        // build $query with Elastica objects
        return $this->findPaginated($query);
    }

    public function findLastMissionsFromOrganization($organization, $limit = 3)
    {
        $query = $this->createQuery([]);

        $queryFilter = $this->createQueryFilter([]);
        $queryFilter = $this->applyOrganizationCriteria($queryFilter, $organization);
        $queryFilter
            ->addMust(new \Elastica\Filter\Term(array('status' => Mission::STATUS_AVAILABLE)))
            ->addMust(new \Elastica\Filter\Term(array('archived' => 0)))
        ;

        $filteredQuery = new \Elastica\Query\Filtered($query, $queryFilter);

        $query = \Elastica\Query::create($filteredQuery);
        $query->addSort(array('published' => array('order' => 'desc')));
        $query->setLimit($limit);

        return $this->findPaginated($query);
    }

    public function findFromAdmin($criteria = null, $orderBy = null)
    {
        $query = $this->createQuery($criteria);

        // Then we create filters depending on the chosen criterias
        $queryFilter = $this->createQueryFilter($criteria);
        // $queryFilter = $this->applyOrganizationCriteria($queryFilter, $organization);

        if (!$orderBy) {
            $orderBy = array('published' => 'desc');
        }

        $filteredQuery = new \Elastica\Query\Filtered($query, $queryFilter);

        $query = \Elastica\Query::create($filteredQuery);
        $query->addSort(array(key($orderBy) => array('order' => $orderBy[key($orderBy)])));

        // build $query with Elastica objects
        return $this->findPaginated($query);
    }

    public function findFromFrontend($criteria = null, $orderBy = null)
    {
        $query = $this->createFrontendQuery($criteria, $orderBy);

        return $this->findPaginated($query);
    }

    public function createFrontendQuery($criteria = null, $orderBy = null)
    {
        $query = $this->createQuery($criteria);

        // Then we create filters depending on the chosen criterias
        $queryFilter = $this->createQueryFilter($criteria)
            ->addMust(new \Elastica\Filter\Term(array('status' => Mission::STATUS_AVAILABLE)))
            ->addMust(new \Elastica\Filter\Term(array('archived' => 0)))
        ;

        if (!$orderBy) {
            $orderBy = array('start_date' => 'asc');
        }

        $filteredQuery = new \Elastica\Query\Filtered($query, $queryFilter);

        $query = \Elastica\Query::create($filteredQuery);
        $query->addSort(array(key($orderBy) => array('order' => $orderBy[key($orderBy)])));

        $agg = new Sum("vacancies_sum");
        $agg->setField("vacancies");

        $query->addAggregation($agg);

        return $query;
    }

    protected function createQueryStringQuery($queryString)
    {
        $queryString = \Elastica\Util::replaceBooleanWordsAndEscapeTerm($queryString);
        $queryString = str_replace('/', '\/', $queryString);

        $query = new QueryString($queryString);
        $query->setDefaultOperator('AND');

        return $query;
    }

    protected function createQuery($criteria)
    {
        $queryString = (isset($criteria['query']) && $criteria['query'] != '') ? $criteria['query'] : null;
        $organization = (isset($criteria['organization']) && $criteria['organization'] != '') ? $criteria['organization'] : null;

        if (!$queryString && !$organization) {
            return new MatchAll();
        }

        $boolQuery = new \Elastica\Query\Bool();

        if ($queryString) {
            $query = $this->createQueryStringQuery($queryString);
            $boolQuery->addMust($query);
        }

        if ($organization) {
            $match = new \Elastica\Query\Match();
            $match
                ->setFieldQuery('organization_name', $organization)
                ->setFieldOperator('organization_name', 'AND');

            $boolQuery->addMust($match);
        }

        return $boolQuery;
    }

    protected function applyOrganizationCriteria($searchQueryFilter, Organization $organization)
    {
        // retreive ids of current $organization + children ids
        $organization_ids = array($organization->getId());

        foreach ($organization->getOrganizations() as $childOrganization) {
            $organization_ids[] = $childOrganization->getId();
        }

        return $searchQueryFilter
            ->addMust(new \Elastica\Filter\Term(array('archived' => 0)))
            ->addMust(new \Elastica\Filter\Terms('organization.id', $organization_ids))
        ;
    }

    protected function createQueryFilter($criteria)
    {
        $queryFilter = new \Elastica\Filter\Bool();

        foreach ($criteria as $key => $value) {
            $value = is_string($value) ? strtolower(trim($value)) : $value;

            if (is_array($value) && empty($value)) {
                continue;
            }

            if (is_string($value) && $value == '') {
                continue;
            }

            switch ($key) {
            case 'taxons':
                // Then we create criteria depending on the chosen criterias
                if (count($value) > 0) {
                    $taxonFilter = new \Elastica\Filter\BoolOr();
                    $taxonFilter->addFilter(new \Elastica\Filter\Terms('taxon_id', $value));
                    $queryFilter->addMust($taxonFilter);
                }
                break;
            case 'start_date':
            case 'published':
                $queryFilter->addMust(new \Elastica\Filter\Range($key,
                    array('gte' => $value)
                ));
            case 'query':
            case 'organization':
                break;
            case 'statuses':
                $queryFilter->addMust(new \Elastica\Filter\Terms('status', $value));
                break;
            case 'country':
                $queryFilter->addMust(new \Elastica\Filter\Terms('country', array(strtoupper($value))));
                break;
            case 'tag':
                $queryFilter->addMust(new \Elastica\Filter\Terms('tag.id', array($value)));
                break;
            default:
                $queryFilter->addMust(
                    new \Elastica\Filter\Term(array($key => $value))
                );
                break;
            }
        }

        return $queryFilter;
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

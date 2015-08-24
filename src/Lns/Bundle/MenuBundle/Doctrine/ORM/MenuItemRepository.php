<?php

namespace Lns\Bundle\MenuBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

use Sylius\Component\Resource\Repository\RepositoryInterface;

class MenuItemRepository extends NestedTreeRepository implements RepositoryInterface
{
    private $resourceRepository;

    public function __construct(EntityManager $em, ClassMetadata $class)
    {
        $this->resourceRepository = new EntityRepository($em, $class);
        parent::__construct($em, $class);
    }

    public function findMenuItemsByMenu($menu, $include_root = false)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('node')
            ->from($this->getClassName(), 'node', 'node.id')
            ->join('node.menu', 'm')
            ->where('m.id = :id');

        if (!$include_root) {
            $qb->andWhere('node.level > 0');
        }

        return $qb->setParameter(':id', $menu->getId())
            ->orderBy('node.left', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->resourceRepository->createNew();
    }

    /**
     * @param mixed $id
     *
     * @return null|object
     */
    public function find($id)
    {
        return $this->resourceRepository->find($id);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->resourceRepository->findAll($id);
    }

    /**
     * @param array $criteria
     *
     * @return null|object
     */
    public function findOneBy(array $criteria)
    {
        return $this->resourceRepository->findOneBy($criteria);
    }

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
        return $this->resourceRepository->findOneBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = null, array $orderBy = null)
    {
        return $this->resourceRepository->createPaginator($criteria, $orderBy);
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return Pagerfanta
     */
    public function getPaginator(QueryBuilder $queryBuilder)
    {
        return $this->resourceRepository->getPaginator($queryBuilder);
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder($alias = null)
    {
        if (is_null($alias)) {
            return parent::getQueryBuilder();
        }

        return $this->createQueryBuilder($alias);
    }

    /**
     * @return QueryBuilder
     */
    protected function getCollectionQueryBuilder($alias = null)
    {
        return $this->getQueryBuilder($alias);
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @param array $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        return $this->resourceRepository->applyCriteria($queryBuilder, $criteria);
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @param array $sorting
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = null)
    {
        return $this->resourceRepository->applySorting($queryBuilder, $sorting);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPropertyName($name)
    {
        return $this->resourceRepository->getPropertyName($name);
    }

    protected function getAlias()
    {
        return 'node';
    }
}

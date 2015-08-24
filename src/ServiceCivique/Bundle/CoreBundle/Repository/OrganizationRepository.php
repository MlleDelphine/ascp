<?php

namespace ServiceCivique\Bundle\CoreBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;
use Doctrine\ORM\Query\ResultSetMapping;

class OrganizationRepository extends EntityRepository
{
    /**
     * createNewHostOrganizationWithName
     *
     * @param string       $organizationName
     * @param Organization $approvedOrganization
     */
    public function createNewHostOrganizationWithName($organizationName, Organization $approvedOrganization)
    {
        $organization = $this->createNewWithName($organizationName);
        $organization->setType(Organization::TYPE_HOST);
        $organization->setApprovedOrganization($approvedOrganization);
        $organization->setApprovalNumber($approvedOrganization->getApprovalNumber());

        return $organization;
    }

    /**
     * createNewWithName
     *
     * @param string $organizationName
     */
    public function createNewWithName($organizationName)
    {
        $organization = $this->createNew();
        $organization->setName($organizationName);

        return $organization;
    }

    public function getAllCount()
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAllWithBatch($offset = 0, $max = 100)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->setMaxResults($max)
            ->setFirstResult($offset)
        ;

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByApprovalNumberLike($approvalNumber)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $truncateApprovalNumber = substr($approvalNumber, 0, -2);

        $queryBuilder
            ->where('o.approvalNumber LIKE :approval')
               ->setParameter('approval', $truncateApprovalNumber . '%')
               ->getQuery();
        ;

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     *
     * @param Organisation $organization
     * @return int
     */
    public function getApplicationCount($organization) {
        return $this->getApplicationCountByStatus($organization);
    }

    /**
     *
     * @param Organisation $organization
     * @return int
     */
    public function getPendingApplicationCount($organization) {
        return $this->getApplicationCountByStatus($organization, Application::WAITING_ANSWER);
    }

    /**
     *
     * @param Organisation $organization
     * @return int
     */
    public function getAcceptedApplicationCount($organization) {
        return $this->getApplicationCountByStatus($organization, Application::POSITIVE_ANSWER);
    }

    /**
     *
     * @param Organisation $organization
     * @return int
     */
    public function getRejectedApplicationCount($organization) {
        return $this->getApplicationCountByStatus($organization, Application::NEGATIVE_ANSWER);
    }

    protected function getApplicationCountByStatus($organization, $status = null) {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('application_count', 'application_count');

        if($status !== null) {
            $sql= <<<SQL
SELECT
    count(distinct a.id) AS application_count
FROM
    organization AS o
    JOIN mission m ON m.organization_id = o.id
    JOIN application a ON m.id = a.mission_id AND a.status = :status
WHERE o.id = :organization_id
SQL;
        } else {
            $sql= <<<SQL
SELECT
    count(distinct a.id) AS application_count
FROM
    organization AS o
    JOIN mission m ON m.organization_id = o.id
    JOIN application a ON m.id = a.mission_id
WHERE o.id = :organization_id
SQL;
        }

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        $query->setParameter('organization_id', $organization->getId());

        if($status !== null) {
            $query->setParameter('status', $status);
        }

        return $query->getSingleScalarResult();
    }
}

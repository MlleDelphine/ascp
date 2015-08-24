<?php

namespace ServiceCivique\Bundle\ExporterBundle\Exporter;

use ServiceCivique\Bundle\UserBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectRepository;
use Departements\Provider;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Doctrine\ORM\Query;
use Symfony\Component\Intl\Intl;

class MissionExporter extends AbstractExporter
{
    protected $departmentProvider;

    /**
     * {@inheritDoc}
     */
    public function __construct(ObjectRepository $repository, Translator $translator, $directory, Provider $departmentProvider)
    {
        $this->departmentProvider = $departmentProvider;
        parent::__construct($repository, $translator, $directory);
    }

    /**
     * getQuery
     * @param  array $parameters
     * @return Query
     *
     * @throw InvalidArgumentException
     */
    public function getQuery($parameters)
    {
        if (!isset($parameters['user'])) {
            throw new InvalidArgumentException('user parameter is missing');
        }

        $user = $parameters['user'];

        $organization_ids = $this->getOrganizationsIds($user->getOrganization());

        $qb = $this->repository->createQueryBuilder('m');

        return $qb
            ->add('where', $qb->expr()->in('m.organization', ':organizations'))
            ->orderBy('m.published', 'DESC')
            ->setParameter('organizations', $organization_ids)
            ->getQuery();
    }

    protected function getOrganizationsIds($organization)
    {
        $organization_ids = array();

        $organization_ids[] = $organization->getId();

        foreach ($organization->getOrganizations() as $childOrganization) {
            $organization_ids[] = $childOrganization->getId();
        }

        return $organization_ids;
    }

    /**
     * getFormater
     * @return Closure
     */
    protected function getFormater()
    {
        return function ($data) {
            $region = $this->departmentProvider->findRegionByCode($data['Région']);

            if ($region) {
                $data['Région'] = $region->getName();
            }

            $departement = $this->departmentProvider->findDepartementByCode($data['Département']);

            if ($departement) {
                $data['Département'] = $departement->getName();
            }

            $data['Pays'] = Intl::getRegionBundle()->getCountryName($data['Pays']);

            $data['Durée'] = $data['Durée'] . ' mois';

            $data['Statut'] = $this->translator->trans('service_civique.mission.status.' . $data['Statut']);

            return $data;
        };
    }

    /**
     * getFields
     * @return array
     */
    protected function getFields()
    {
        return array(
            'Titre'                  => 'title',
            'Description'            => 'description',
            'Organisme'              => 'organizationName',
            'Région'                 => 'area',
            'Département'            => 'department',
            'Pays'                   => 'country',
            'Nombre de candidatures' => 'applicationCount',
            'Date de publication'    => 'published',
            'Date de début'          => 'startDate',
            'Durée'                  => 'duration',
            'Statut'                 => 'status',
        );
    }
}

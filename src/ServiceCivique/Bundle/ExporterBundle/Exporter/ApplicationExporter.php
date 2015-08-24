<?php

namespace ServiceCivique\Bundle\ExporterBundle\Exporter;

use ServiceCivique\Bundle\UserBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Intl\Intl;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class ApplicationExporter extends AbstractExporter
{
    /**
     * __construct
     *
     * @param ObjectRepository $repository
     * @param Translator       $translator
     * @param mixed            $directory
     * @param Router           $router
     */
    public function __construct(ObjectRepository $repository, Translator $translator, $directory, Router $router)
    {
        $this->router = $router;
        parent::__construct($repository, $translator, $directory);
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery($parameters)
    {
        if (!isset($parameters['mission'])) {
            throw new InvalidArgumentException('mission parameters is missing');
        }

        $mission = $parameters['mission'];

        $qb = $this->repository->createQueryBuilder('a');

        return $qb
            ->where('a.mission = :mission')
            ->join('a.user', 'u')
            ->join('a.mission', 'm')
            ->orderBy('a.created', 'DESC')
            ->setParameter('mission', $mission)
            ->getQuery();
    }

    /**
     * getFormater
     * @return Closure
     */
    protected function getFormater()
    {
        return function ($data) {

            $data['Statut'] = $this->translator->trans('service_civique.application.status.' . $data['Statut']);

            if ($data['Cv']) {
                $data['Cv'] = $this->router->generate('service_civique_resume_show', array('slug' => $data['Cv']), true);
            }

            $data['Pays'] = Intl::getRegionBundle()->getCountryName($data['Pays']);

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
            'Mission'             => 'mission.title',
            'Date de candidature' => 'created',
            'Candidat'            => 'user.fullname',
            'Téléphone'           => 'phone_number',
            'Email'               => 'user.email',
            'Motivation'          => 'motivation',
            'Cv'                  => 'path',
            'Code Postal'         => 'zipcode',
            'Ville'               => 'city',
            'Pays'                => 'country',
            'Statut'              => 'status',
        );
    }
}

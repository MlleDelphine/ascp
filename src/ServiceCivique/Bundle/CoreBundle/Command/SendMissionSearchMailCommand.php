<?php

namespace ServiceCivique\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\CoreBundle\Entity\MissionSearch;
use ServiceCivique\Bundle\CoreBundle\Repository\MissionSearchRepository;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

class SendMissionSearchMailCommand extends ContainerAwareCommand
{

    protected $producer;
    protected $missionSearchRepository;

    public function __construct(Producer $producer, MissionSearchRepository $missionSearchRepository)
    {
        $this->producer = $producer;
        $this->missionSearchRepository = $missionSearchRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('sc:mail:missionsearch')
            ->setDescription('Send missionsearch results by mail')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $missionSearchs = $this->missionSearchRepository->findAll();

        foreach ($missionSearchs as $missionSearch) {
            $message = array(
                'recipient'     => $missionSearch->getUser()->getEmail(),
                'missionSearch' => $missionSearch,
                'criteria'      => $this->buildSearchCriteria($missionSearch)
            );
            $this->producer->publish(serialize($message));
        }
    }

    protected function buildSearchCriteria(MissionSearch $missionSearch)
    {
        $criteria = array(
            'is_overseas' => (integer) $missionSearch->getIsOverseas()
        );
        if ($missionSearch->getStartDate() instanceof \Datetime) {
            $criteria['start_date'] = $missionSearch->getStartDate()->format('Y-m-d');
        }
        if (count($missionSearch->getTaxons()) > 0) {
            $taxonIds = array();
            foreach ($missionSearch->getTaxons() as $taxon) {
                $taxonIds[] = $taxon->getId();
            }
            $criteria['taxons'] = $taxonIds;
        }
        if ($missionSearch->getQuery() != null) {
            $criteria['query'] = $missionSearch->getQuery();
        }
        if ($missionSearch->getOrganization() != null) {
            $criteria['organization'] = $missionSearch->getOrganization();
        }
        if ($missionSearch->getCountry() != null) {
            $criteria['country'] = $missionSearch->getCountry();
        }
        if ($missionSearch->getArea() != null) {
            $criteria['area'] = $missionSearch->getArea();
        }
        if ($missionSearch->getDepartment() != null) {
            $criteria['department'] = $missionSearch->getDepartment();
        }

        return $criteria;
    }
}

<?php

namespace ServiceCivique\Bundle\ArchiveBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\CoreBundle\Repository\MissionRepository;

class ArchiveMissionsCommand extends ContainerAwareCommand
{

    protected $repository;

    public function __construct(MissionRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('sc:archive:missions')
            ->setDescription('Archive sc missions')
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, 'Specify date before wich a mission is archived (format: dd/mm/YYYY)', '24/10/2010')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Specify number of missions to archive', 500);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $datetime = $input->getOption('date') ? \Datetime::createFromFormat('d/m/Y', $input->getOption('date')) : \Datetime('2 years ago');
        $limit = $input->getOption('limit');

        $missions = $this->repository->findToBeArchived($datetime, $limit);
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $flushInterval = 100;
        $i = 0;

        foreach ($missions as $mission) {
            $mission->setArchived(true);
            $em->persist($mission);
            if ($i % $flushInterval) {
               $em->flush();
            }
            $i++;
        }

        $output->writeln($i . ' missions archivées');
    }

}

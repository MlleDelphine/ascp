<?php

namespace ServiceCivique\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;

class UpdateMissionStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:update:missions-status')
            ->setDescription('Update mission status for applications')
        ;
    }

    // Set missionStatus in each application
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = 100;
        $applicationBatchSize = 30;

        $missionRepository = $this->getContainer()->get('service_civique.repository.mission');
        $missionCount = $missionRepository->getAllCount();

        // Batching
        // We don't find all missions because of memory limit
        for ($i = 0; $i < $missionCount; $i += $batchSize) {
            $missions = $missionRepository->findBy(
               array(),
               array(),
               $batchSize,
               $i
            );

            foreach ($missions as $mission) {
                $output->writeln($mission->getId());
                $missionStatusService = $this->getContainer()->get('service_civique.service.mission_status_service');
                // why I did that ??
                $missionStatusService->setApplicationMissionStatus($mission, $applicationBatchSize);
            }
        }

    }
}

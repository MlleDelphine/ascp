<?php

namespace ServiceCivique\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FormatApprovalNumbersMissionByOrganizationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:format:approvals_mission_by_organization')
            ->setDescription('Format all approval numbers of missions with the latest one of organization')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = 1000;
        $missionRepository = $this->getContainer()->get('service_civique.repository.mission');
        $em = $this->getContainer()->get('Doctrine')->getManager();
        $missionCount = $missionRepository->getAllCount();
        $output->writeln('==== Processing: ' . $missionCount . ' missions ====');

        // Batching
        for ($i = 0; $i < $missionCount; $i += $batchSize) {
            $missionGroup = $missionRepository->getAllWithBatch($i, $batchSize);
            foreach ($missionGroup as $mission) {
                if ($mission->getOrganization()) {
                    $organizationApprovalNumber = $mission->getOrganization()->getApprovalNumber();
                    $mission->setApprovalNumber($organizationApprovalNumber);
                } else {
                    $output->writeln($mission->getId());
                }
            }
            $em->flush();
            $em->clear();
        }
    }
}

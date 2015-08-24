<?php

namespace ServiceCivique\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FormatApprovalNumbersMissionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:format:approvals_mission')
            ->setDescription('Format all approval numbers of missions')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = 1000;
        $missionRepository = $this->getContainer()->get('service_civique.repository.mission');
        $em = $this->getContainer()->get('Doctrine')->getManager();
        $missionCount = $missionRepository->getAllCount();
        $output->writeln('==== Processing: ' . $missionCount . ' missions ====');
        $pattern = '/^[A-Z]{2}-[0-9]{3}-[0-9]{2}-[0-9]{5}(?:-[0-9]{2}|$)$/';
        $toRemove = array(' ', '.', '-', '_', '‐');

        // Batching
        for ($i = 0; $i < $missionCount; $i += $batchSize) {
            $missionGroup = $missionRepository->getAllWithBatch($i, $batchSize);
            foreach ($missionGroup as $mission) {
                if (strlen($mission->getApprovalNumber()) > 18) {
                    $approvalNumber = str_replace($toRemove, '', $mission->getApprovalNumber());
                    $approvalNumber = strtoupper($approvalNumber);
                    if (strlen($approvalNumber) >= 12) {
                        $newApprovalNumber =
                            $approvalNumber[0].$approvalNumber[1] . '-' .
                            $approvalNumber[2].$approvalNumber[3].$approvalNumber[4] . '-' .
                            $approvalNumber[5].$approvalNumber[6] . '-' .
                            $approvalNumber[7].$approvalNumber[8].$approvalNumber[9].$approvalNumber[10].$approvalNumber[11]
                        ;
                        if (isset($approvalNumber[12]) && isset($approvalNumber[13]) ) {
                            $newApprovalNumber .=  '-' . $approvalNumber[12].$approvalNumber[13];
                        }

                        if (preg_match($pattern, $newApprovalNumber)) {
                            $mission->setApprovalNumber($newApprovalNumber);
                        } else {
                            $output->writeln($mission->getId() . ' === ' . $mission->getApprovalNumber());
                            $mission->setApprovalNumber('XX-000-00-00000-00');
                        }
                    } else {
                        $output->writeln('TOO SHORT:' . $mission->getId() . ' === ' . $mission->getApprovalNumber());
                        $mission->setApprovalNumber('XX-000-00-00000-00');
                    }
                    $em->persist($mission);
               }
               if (preg_match($pattern, $mission->getApprovalNumber())) {
                    if ($mission->getOrganization()) {
                        $mission->setApprovalNumber($mission->getOrganization()->getApprovalNumber());
                    } else {
                        $output->writeln($mission->getId());
                    }
                }
            }
            $em->flush();
            $em->clear();
        }
    }
}

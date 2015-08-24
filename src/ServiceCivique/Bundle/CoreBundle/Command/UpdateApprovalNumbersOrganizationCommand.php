<?php

namespace ServiceCivique\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateApprovalNumbersOrganizationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:update:approvals_organization')
            ->setDescription('Format all approval numbers of missions with the latest one of organization')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = 1000;
        $approvalRepository = $this->getContainer()->get('service_civique.repository.approval');
        $organizationRepository = $this->getContainer()->get('service_civique.repository.organization');
        $em = $this->getContainer()->get('Doctrine')->getManager();
        $approvalCount = $approvalRepository->getAllCount();
        $output->writeln('==== Processing: ' . $approvalCount . ' approvals ====');

        // Batching
        for ($i = 0; $i < $approvalCount; $i += $batchSize) {
            $approvalGroup = $approvalRepository->getAllWithBatch($i, $batchSize);
            foreach ($approvalGroup as $approval) {

                $organizations = $organizationRepository->findByApprovalNumberLike($approval->getApprovalNumber());
                $output->writeln('====' . $approval->getApprovalNumber() . '====');
                foreach ($organizations as $organization) {
                    if ($organization->getLastDecisionDate() < $approval->getDecisionDate()) {
                        $organization->setPreviousApprovalNumber($organization->getApprovalNumber());
                        $organization->setApprovalNumber($approval->getApprovalNumber());
                        $organization->setLastDecisionDate($approval->getDecisionDate());
                        $output->writeln('  ' . $organization->getName());
                    }
                }
            }
            $em->flush();
            $em->clear();
        }
    }
}

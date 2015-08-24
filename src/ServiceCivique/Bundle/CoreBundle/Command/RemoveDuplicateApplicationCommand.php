<?php

namespace ServiceCivique\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;

class RemoveDuplicateApplicationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:clean:application')
            ->setDescription('Delete all duplicate applications')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = 1000;

        $applicationRepository = $this->getContainer()->get('service_civique.repository.application');
        $em = $this->getContainer()->get('Doctrine')->getManager();
        $applicationCount = $applicationRepository->getAllWithDuplicatesCount();

        // Batching
        for ($i = 0; $i < $applicationCount; $i += $batchSize) {
            $applicationsGroup = $applicationRepository->getAllWithDuplicates($i, $batchSize);
            foreach ($applicationsGroup as $applicationGroup) {
                $output->writeln(
                    $applicationGroup[0]->getMission()->getId() . ' - ' . $applicationGroup[0]->getUser()->getId() . ' => ' . $applicationGroup['cpt']
                );
                $applications = $applicationRepository->findBy(
                    array(
                        'mission' => $applicationGroup[0]->getMission()->getId(),
                        'user'    => $applicationGroup[0]->getUser()->getId(),
                    ),
                    array('id' => 'DESC')
                );

                // Keep the last application
                array_shift($applications);
                foreach ($applications as $application) {
                    $output->writeln(' - ' . $application->getId());
                    $em->remove($application);
                }
                $em->flush();
                $em->clear();
            }
        }
    }
}

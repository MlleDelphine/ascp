<?php

namespace ServiceCivique\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FormatApprovalNumbersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:format:approvals')
            ->setDescription('Format all approval numbers of organizations')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = 1000;

        $organizationRepository = $this->getContainer()->get('service_civique.repository.organization');
        $em = $this->getContainer()->get('Doctrine')->getManager();
        $organizationCount = $organizationRepository->getAllCount();
        $alreadyFormatted = 0;
        $formattable = 0;
        // $pattern = '/[A-Z0-9]{2}-[A-Z0-9]{3}-[A-Z0-9]{2}-[A-Z0-9]{5}-[A-Z0-9]{2}/';
        $pattern = '/^[A-Z]{2}-[0-9]{3}-[0-9]{2}-[0-9]{5}(?:-[0-9]{2}|$)$/';
        $toRemove = array(' ', '.', '-', '_', '‐');

        $output->writeln('==== Processing: ' . $organizationCount . ' organizations ====');

        // $dates = array();
        $oldToFormat    = 0;
        $recentToFormat = 0;
        $oldToFormat    = 0;
        $toto           = 0;
        $recent         = 0;
        $noan           = 0;

        // Batching
        for ($i = 0; $i < $organizationCount; $i += $batchSize) {
            $organizationGroup = $organizationRepository->getAllWithBatch($i, $batchSize);
            foreach ($organizationGroup as $organization) {
                if (preg_match($pattern, $organization->getApprovalNumber())) {
                    $alreadyFormatted++;
                    // $output->writeln($organization->getApprovalNumber());
                } else {
                    if ($organization->getApprovalNumber()) {
                        $approvalNumber = str_replace($toRemove, '', $organization->getApprovalNumber());
                        // $output->writeln($approvalNumber);
                        $simplePattern = '/^[A-Z]{2}([0-9]{10}|[0-9]{12})$/';
                        if (preg_match($simplePattern, $approvalNumber)) {
                            $formattable++;
                            $newApprovalNumber =
                                $approvalNumber[0].$approvalNumber[1] . '-' .
                                $approvalNumber[2].$approvalNumber[3].$approvalNumber[4] . '-' .
                                $approvalNumber[5].$approvalNumber[6] . '-' .
                                $approvalNumber[7].$approvalNumber[8].$approvalNumber[9].$approvalNumber[10].$approvalNumber[11]
                            ;
                            if (isset($approvalNumber[12]) && isset($approvalNumber[13]) ) {
                                $newApprovalNumber .=  '-' . $approvalNumber[12].$approvalNumber[13];
                            }
                            $organization->setApprovalNumber($newApprovalNumber);
                        } else {
                            if ($organization->getUser() && $organization->getUser()->getLastlogin()) {
                                $date = $organization->getUser()->getLastlogin();
                                if ($date->format('Y') == 2014 || $date->format('Y') == 2015) {
                                    $recentToFormat++;
                                    $organization->setTodo(1);
                                    $em->persist($organization);
                                }
                                $recent++;
                                $organization->setApprovalNumber('XX-000-00-00000-00');
                            } else {
                                $oldToFormat++;
                                $organization->setApprovalNumber('XX-000-00-00000-00');
                            }
                            $toto++;
                        }
                    } else {
                        $noan++;
                        $organization->setApprovalNumber('XX-000-00-00000-00');
                    }
                }
            }
            $em->flush();
            $em->clear();
        }
        $shit = $organizationCount - ($alreadyFormatted + $formattable);
        $output->writeln('Already formatted: ' . $alreadyFormatted);
        $output->writeln('Formattable: ' . $formattable);
        $output->writeln('Shit: ' . $shit);
        $output->writeln('Recent, need manual format: ' . $recentToFormat);
        $output->writeln('Old, need manual format: ' . $oldToFormat);
        $output->writeln('Recent: ' . $recent);
        $output->writeln('Nope: ' . $toto);
        $output->writeln('noan: ' . $noan);
        // var_dump(array_count_values($dates));
    }
}

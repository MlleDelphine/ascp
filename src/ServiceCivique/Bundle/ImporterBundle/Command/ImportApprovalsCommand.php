<?php

namespace ServiceCivique\Bundle\ImporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\CoreBundle\Entity\Approval;

class ImportApprovalsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:import:approvals')
            ->setDescription('Import approvals')
            ->addArgument('filePath', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $batchSize = 1;
        // $i = 0;

        // $em = $this->getContainer()->get('Doctrine')->getManager();
        // $csvPath = $input->getArgument('filePath');
        // if (($handle = fopen($csvPath, "r")) !== FALSE) {
        //     while (($row = fgetcsv($handle)) !== FALSE) {
        //         $approval = new Approval();
        //         $approval->setOrganizationName($row[0]);
        //         $approval->setApprovalNumber($row[1]);
        //         $approval->setDecisionDate($row[2]);
        //         $approval->setTermDate($row[3]);
        //         $approval->setJobNumber($row[4]);
        //         $approval->setTaxonomy($row[5]);
        //         $approval->setReferentAddress($row[6]);
        //         $approval->setPdfUrl($row[7]);

        //         $em->persist($approval);
        //         $output->writeln($row[0]);
        //         $i++;
        //         if (($i % $batchSize) == 0) {
        //             $em->flush();
        //             $em->clear();
        //         }
        //     }
        // }
    }
}

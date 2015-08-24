<?php

namespace ServiceCivique\Bundle\ImporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\ImporterBundle\DataImport\Reader\JsonLinesReader;
use ServiceCivique\Bundle\ImporterBundle\DataImport\MissionEmailWorkflow;
use Ddeboer\DataImport\Writer\ConsoleProgressWriter;

class ImportMissionsEmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:import:missions:emails')
            ->setDescription('Import sc mission emails')
            ->addArgument('file', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reader = new JsonLinesReader($input->getArgument('file'));

        $missionWorkflow = new MissionEmailWorkflow($reader, $this->getContainer()->get('logger'));
        $missionWorkflow
            ->setSkipItemOnFailure(true)
            ->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'))
            ->build()
            ->addWriter(new ConsoleProgressWriter($output, $reader))
            ->process();
    }
}

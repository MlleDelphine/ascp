<?php

namespace ServiceCivique\Bundle\ImporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\ImporterBundle\DataImport\Reader\JsonLinesReader;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ApplicationWorkflow;
use Ddeboer\DataImport\Writer\ConsoleProgressWriter;

class ImportApplicationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:import:applications')
            ->setDescription('Import sc users')
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('step', null, InputOption::VALUE_OPTIONAL, 'Specify number of item per file', 1000);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reader = new JsonLinesReader($input->getArgument('file'));

        $applicationWorkflow = new ApplicationWorkflow($reader, $this->getContainer()->get('logger'));
        $applicationWorkflow
            ->setSkipItemOnFailure(true)
            ->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'))
            ->build()
            ->addWriter(new ConsoleProgressWriter($output, $reader))
            ->process();
    }
}

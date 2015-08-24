<?php

namespace ServiceCivique\Bundle\ImporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\ImporterBundle\DataImport\Reader\JsonLinesReader;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ActuWorkflow;
use Ddeboer\DataImport\Writer\ConsoleProgressWriter;

class ImportActusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:import:actus')
            ->setDescription('Import sc actu')
            ->addArgument('file', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reader = new JsonLinesReader($input->getArgument('file'));

        $actuWorkflow = new ActuWorkflow($reader, $this->getContainer()->get('logger'));
        $actuWorkflow
            ->setSkipItemOnFailure(false)
            ->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'))
            ->build()
            ->addWriter(new ConsoleProgressWriter($output, $reader))
            ->process();
    }
}

<?php

namespace ServiceCivique\Bundle\ImporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\ImporterBundle\DataImport\Reader\JsonLinesReader;
use ServiceCivique\Bundle\ImporterBundle\DataImport\MissionWorkflow;
use Ddeboer\DataImport\Writer\ConsoleProgressWriter;

class ImportMissionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:import:missions')
            ->setDescription('Import sc users')
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('step', null, InputOption::VALUE_OPTIONAL, 'Specify number of item per file', 1000);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reader = new JsonLinesReader($input->getArgument('file'));

        $missionWorkflow = new MissionWorkflow($reader, $this->getContainer()->get('logger'));
        $missionWorkflow
            ->setSkipItemOnFailure(true)
            ->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'))
            ->setDepartementsProvider($this->getContainer()->get('polem_departements.provider'))
            ->setPhoneNumberUtil($this->getContainer()->get('libphonenumber.phone_number_util'))
            ->build()
            ->addWriter(new ConsoleProgressWriter($output, $reader))
            ->process();
    }
}

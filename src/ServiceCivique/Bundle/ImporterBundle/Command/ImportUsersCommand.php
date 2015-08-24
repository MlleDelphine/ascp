<?php

namespace ServiceCivique\Bundle\ImporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\ImporterBundle\DataImport\Reader\JsonLinesReader;
use ServiceCivique\Bundle\ImporterBundle\DataImport\UserWorkflow;
use ServiceCivique\Bundle\ImporterBundle\DataImport\OrganizationWorkflow;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ProfileWorkflow;
use Ddeboer\DataImport\Writer\ConsoleProgressWriter;

class ImportUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:import:users')
            ->setDescription('Import sc users')
            ->addArgument('file', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reader = new JsonLinesReader($input->getArgument('file'));

        $output->writeln('<info>Importing users …</info>');

        $userWorkflow = new UserWorkflow($reader, $this->getContainer()->get('logger'));
        $userWorkflow
            ->setSkipItemOnFailure(true)
            ->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'))
            ->build()
            ->addWriter(new ConsoleProgressWriter($output, $reader))
            ->process();

        $output->writeln('<info>Importing organizations …</info>');

        $organizationWorkflow = new OrganizationWorkflow($reader, $this->getContainer()->get('logger'));
        $organizationWorkflow
            ->setSkipItemOnFailure(true)
            ->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'))
            ->setDepartementsProvider($this->getContainer()->get('polem_departements.provider'))
            ->setPhoneNumberUtil($this->getContainer()->get('libphonenumber.phone_number_util'))
            ->build()
            ->addWriter(new ConsoleProgressWriter($output, $reader))
            ->process();

        $output->writeln('<info>Importing profiles …</info>');

        $profileWorkflow = new ProfileWorkflow($reader, $this->getContainer()->get('logger'));
        $profileWorkflow
            ->setSkipItemOnFailure(true)
            ->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'))
            ->setDepartementsProvider($this->getContainer()->get('polem_departements.provider'))
            ->build()
            ->addWriter(new ConsoleProgressWriter($output, $reader))
            ->process();
    }
}

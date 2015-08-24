<?php

namespace ServiceCivique\Bundle\ImporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceCivique\Bundle\ImporterBundle\DataImport\Reader\MultipleJsonReader;
use ServiceCivique\Bundle\ImporterBundle\DataImport\TaxonomyWorkflow;
use Symfony\Component\Finder\Finder;
use Ddeboer\DataImport\Writer\ConsoleProgressWriter;

class ImportTaxonomiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sc:import:taxonomies')
            ->setDescription('Import sc taxonomies')
            ->addArgument('dir', InputArgument::REQUIRED)
            ->addOption('step', null, InputOption::VALUE_OPTIONAL, 'Specify number of item per file', 1000);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder->files()->in($input->getArgument('dir'))->name('taxonomies*.json');

        $reader = new MultipleJsonReader($finder->getIterator(), 'taxonomies', $input->getOption('step'));

        $missionWorkflow = new TaxonomyWorkflow($reader, $this->getContainer()->get('logger'));
        $missionWorkflow
            ->setSkipItemOnFailure(true)
            ->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'))
            ->build()
            ->addWriter(new ConsoleProgressWriter($output, $reader))
            ->process();
    }
}

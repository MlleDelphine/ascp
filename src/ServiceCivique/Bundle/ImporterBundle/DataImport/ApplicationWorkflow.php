<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport;

use Ddeboer\DataImport\ValueConverter;
use Ddeboer\DataImport\Filter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ItemConverter\MappingItemConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\OriginalIdToIdValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter\CvFilePathValueConverter;

class ApplicationWorkflow extends Workflow
{
    public function addFilters()
    {
        $this->addFilterAfterConversion(new Filter\CallbackFilter(function ($data) {
            return isset($data['user']) && isset($data['mission']);
        }));
    }

    public function addItemsConverters()
    {
        $mappingItemConverter = new MappingItemConverter();
        $mappingItemConverter
            ->addMapping('[id]', '[originalId]')
            ->addMapping('[mid]', '[mission]')
            ->addMapping('[uid]', '[user]')
            ->addMapping('[submitted]', '[created]')
            ->addMapping('[message_subject]', '[messageSubject]')
            ->addMapping('[message_text]', '[messageText]')
            ->addMapping('[response_status]', '[status]')
            ->addMapping('[response_date]', '[messageDate]')
            ->addMapping('[curriculum][filepath]', '[cv]')
            ->addMapping('[candidature][address]', '[address]')
            ->addMapping('[candidature][zipcode]', '[zipCode]')
            ->addMapping('[candidature][city]', '[city]')
            ->addMapping('[candidature][motivation]', '[motivation]')
            ->addMapping('[candidature][phone]', '[phoneNumber]')
        ;
        $this->addItemConverter($mappingItemConverter);
    }

    public function addValueConverters()
    {
        $connection = $this->getEntityManager()->getConnection();

        $this
            ->addValueConverter('mission', new OriginalIdToIdValueConverter($connection, 'mission'))
            ->addValueConverter('user', new OriginalIdToIdValueConverter($connection, 'user'))
            ->addValueConverter('cv', new CvFilePathValueConverter())
            ->addValueConverter('created', new ValueConverter\DateTimeValueConverter('U'))
            ->addValueConverter('messageDate', new ValueConverter\DateTimeValueConverter('U'));
    }

    protected function getEntityClass()
    {
        return 'ServiceCiviqueCoreBundle:Application';
    }
}

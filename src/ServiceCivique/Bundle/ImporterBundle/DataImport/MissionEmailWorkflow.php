<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport;

use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use Ddeboer\DataImport\ItemConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ItemConverter\MappingItemConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\Writer\Writer;

/**
 * Class: MissionWorkflow
 */
class MissionEmailWorkflow extends Workflow
{
    public function addItemsConverters()
    {
        $mappingItemConverter = new MappingItemConverter();
        $mappingItemConverter
            ->addMapping('[nid]', '[originalId]')
            ->addMapping('[field_email_contact][0][email]', '[additionalEmailContact]')
            ;

        $this->addItemConverter($mappingItemConverter);

        $this->addItemConverter(new ItemConverter\CallbackItemConverter(function ($item) {
            $newItem = array(
                'originalId'             => $item['originalId'],
                'additionalEmailContact' => $item['additionalEmailContact']
            );

            return $newItem;
        }));

    }

    protected function addWriters()
    {
        $writer = new Writer($this->getEntityManager(), $this->getEntityClass(), 'originalId', $this->logger, Writer::MODE_UPDATE_ONLY);
        $this->addWriter($writer);
    }

    protected function getEntityClass()
    {
        return 'ServiceCiviqueCoreBundle:Mission';
    }
}

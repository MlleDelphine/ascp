<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport;

use Ddeboer\DataImport\ItemConverter;
use Ddeboer\DataImport\ValueConverter;
use ServiceCivique\Bundle\ImporterBundle\DataImport\ItemConverter\MappingItemConverter;
use Gedmo\Sluggable\Util\Urlizer;

class ActuWorkflow extends Workflow
{
    protected $departements_provider;
    protected $phone_number_util;
    protected $taxonIndex;

    public function addItemsConverters()
    {
        $mappingItemConverter = new MappingItemConverter();
        $mappingItemConverter
            ->addMapping('[teaser]', '[description]')
            ->addMapping('[body]', '[text]')
            ->addMapping('[created]', '[published]')
            ->addMapping('[path]', '[slug]')
            ;

        $this->addItemConverter($mappingItemConverter);

        $this->addItemConverter(new ItemConverter\CallbackItemConverter(function ($item) {
            $item['description'] = mb_substr(html_entity_decode(strip_tags($item['description'])), 0 , 230, 'utf-8');
            $item['slug'] = \Gedmo\Sluggable\Util\Urlizer::urlize($item['title']);

            return $item;
        }));
    }

    public function addValueConverters()
    {
        $this
            ->addValueConverter('published', new ValueConverter\DateTimeValueConverter('U'));
    }

    protected function getEntityClass()
    {
        return 'ServiceCiviqueCoreBundle:Actu';
    }
}

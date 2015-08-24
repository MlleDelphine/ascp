<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport;

use Ddeboer\DataImport\Writer;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;

/**
 * Class TaxonomyWorkflow
 */
class TaxonomyWorkflow extends Workflow
{
    public function addItemsConverters()
    {
        $this->addItemConverter(new CallbackItemConverter(function ($item) {
            $name = key($item);
            $item['name'] = $name;

            $item['taxons'] = array();

            foreach ($item[$name] as $term) {
                $item['taxons'][] = array(
                    'name' => $term['name']
                );
            }

            unset($item[$name]);

            return $item;
        }));
    }

    protected function addWriters()
    {
        $em = $this->getEntityManager();

        $taxonRepository = $em->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Taxon');
        $taxonomyRepository = $em->getRepository($this->getEntityClass());

        $writer = new Writer\CallbackWriter(function ($item) use ($taxonomyRepository, $taxonRepository) {

            $taxonomy = $taxonomyRepository->findOneByName($item['name']);

            if (!$taxonomy) {
                $taxonomy = new \ServiceCivique\Bundle\CoreBundle\Entity\Taxonomy();
            }

            $taxonomy->setName($item['name']);

            foreach ($item['taxons'] as $taxonDatas) {
                $taxon = $taxonRepository->findOneByName($taxonDatas['name']);
                if (!$taxon) {
                    $taxon = new \ServiceCivique\Bundle\CoreBundle\Entity\Taxon();
                }
                $taxon->setName($taxonDatas['name']);

                $taxonomy->addTaxon($taxon);
            }

            $this->getEntityManager()->persist($taxonomy);
            $this->getEntityManager()->flush();

        });

        $this->addWriter($writer);
    }

    protected function getEntityClass()
    {
        return 'ServiceCiviqueCoreBundle:Taxonomy';
    }
}

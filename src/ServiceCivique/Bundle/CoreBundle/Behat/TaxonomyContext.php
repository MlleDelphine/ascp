<?php

namespace ServiceCivique\Bundle\CoreBundle\Behat;

use Behat\Gherkin\Node\TableNode;

class TaxonomyContext extends DefaultContext
{
    /**
     * @Given /^I created taxonomy "([^""]*)"$/
     */
    public function thereIsTaxonomy($name, $flush = true)
    {
        $taxonomy = $this->getRepository('taxonomy')->createNew();
        $taxonomy->setName($name);

        $this->getEntityManager()->persist($taxonomy);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @Given /^there are following taxonomies defined:$/
     */
    public function thereAreFollowingTaxonomies(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsTaxonomy($data['name'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^taxonomy "([^""]*)" has following taxons:$/
     */
    public function taxonomyHasFollowingTaxons($taxonomyName, TableNode $taxonsTable)
    {
        $taxonomy = $this->findOneByName('taxonomy', $taxonomyName);
        $manager = $this->getEntityManager();

        $taxons = array();

        foreach ($taxonsTable->getRows() as $node) {
            $taxonList = explode('>', $node[0]);
            $parent = null;

            foreach ($taxonList as $taxonName) {
                $taxonName = trim($taxonName);

                if (!isset($taxons[$taxonName])) {
                    /* @var $taxon TaxonInterface */
                    $taxon = $this->getRepository('taxon')->createNew();
                    $taxon->setName($taxonName);

                    $taxons[$taxonName] = $taxon;
                }

                $taxon = $taxons[$taxonName];

                if (null !== $parent) {
                    $parent->addChild($taxon);
                } else {
                    $taxonomy->addTaxon($taxon);
                }

                $parent = $taxon;
            }
        }

        $manager->persist($taxonomy);
        $manager->flush();
    }
}

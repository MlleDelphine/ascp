<?php

namespace ServiceCivique\Bundle\CoreBundle\Entity;

use Sylius\Component\Taxonomy\Model\Taxonomy as BaseTaxonomy;

class Taxonomy extends BaseTaxonomy
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->setRoot(new Taxon());
    }
}

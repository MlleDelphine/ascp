<?php

namespace ServiceCivique\Bundle\AddressingBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

use ServiceCivique\Bundle\AddressingBundle\Entity\Commune;

class LoadTaxonomyData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $conn = $manager->getConnection();

        $file = __DIR__.'/data/taxonomy.sql';
        $data = file_get_contents($file);


        $conn->executeUpdate($data);

    }
}

<?php

namespace ServiceCivique\Bundle\AddressingBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

use ServiceCivique\Bundle\AddressingBundle\Entity\Commune;

class LoadCommuneData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $communesDatas = json_decode(file_get_contents(__DIR__ . '/../../Resources/datas/communes.json'), true);

        $i = 0;

        foreach ($communesDatas as $code => $communeDatas) {

            foreach ($communeDatas as $data) {

                $commune = new Commune();
                $commune->setDepartment($data['department']);
                $commune->setArea($data['area']);
                $commune->setName($data['name']);
                $commune->setZipCode($code);
                $manager->persist($commune);

                $i++;

                if ($i % 25000 == 0) {
                    $manager->flush();
                }
            }
        }

        $manager->flush();
    }
}

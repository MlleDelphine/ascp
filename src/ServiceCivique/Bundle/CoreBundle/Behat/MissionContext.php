<?php

namespace ServiceCivique\Bundle\CoreBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;

class MissionContext extends DefaultContext
{
    /**
     * @Given /^there are following missions:$/
     * @Given /^the following missions exist:$/
     * @Given /^there are missions:$/
     */
    public function thereAreMission(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $missionRepository = $this->getRepository('mission');

        $status_map = array(
            'available' => Mission::STATUS_AVAILABLE,
            'filled'    => Mission::STATUS_FILLED,
            'draft'     => Mission::STATUS_DRAFT
        );

        foreach ($table->getHash() as $data) {

            // default values
            $data['country']     = isset($data['country']) ? $data['country'] : 'FR';
            $data['is_overseas'] = isset($data['is_overseas']) ? $data['is_overseas'] : ($data['country'] != 'FR');
            $data['archived']    = isset($data['archived']) ? $data['archived'] : false;
            $data['status']      = isset($data['status']) ? $data['status'] : Mission::STATUS_AVAILABLE;
            $data['area']        = isset($data['area']) ? $data['area'] : null;
            $data['departement'] = isset($data['departement']) ? $data['departement'] : null;

            $mission = $missionRepository->createNew();

            $mission
                ->setTitle($data['title'])
                ->setDepartment($data['departement'])
                ->setArea($data['area'])
                ->setDescription($data['description'])
                ->setStartDate(new \DateTime($data['startDate']))
                ->setDuration(6)
                ->setWeeklyWorkingHours(35)
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->setPublished(new \DateTime())
                ->setStatus(1)
                ->setVacancies(1)
                ->setCountry($data['country'])
                ->setIsOverseas($data['is_overseas'])
                ->setArchived($data['archived'])
                ->setContact('')
                ->setStatus($status_map[$data['status']])
            ;

            if (isset($data['taxons'])) {
                $taxon = $this->findOneByName('taxon', trim($data['taxons']));
                $mission->setTaxon($taxon);
            }

            if (isset($data['organization'])) {
                $organization = $this->findOneByName('organization', trim($data['organization']));
                $mission->setOrganization($organization);
            }

            $manager->persist($mission);
        }

        $manager->flush();
    }
}

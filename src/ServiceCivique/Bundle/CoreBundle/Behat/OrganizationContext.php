<?php

namespace ServiceCivique\Bundle\CoreBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Context\SnippetAcceptingContext;

class OrganizationContext extends DefaultContext implements SnippetAcceptingContext
{
    /**
     * @Given /^there are organizations:$/
     */
    public function thereAreOrganizations(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $organizationRepository = $this->getRepository('organization');

        foreach ($table->getHash() as $data) {
            $organization = $organizationRepository->createNew();
            $organization->setName($data['name']);
            $organization->setArchived(0);

            $manager->persist($organization);
        }

        $manager->flush();
    }
}

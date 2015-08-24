<?php

namespace ServiceCivique\Bundle\CoreBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Symfony\Component\PropertyAccess\StringUtil;
use Behat\Behat\Context\SnippetAcceptingContext;

class BaseContext extends DefaultContext implements SnippetAcceptingContext
{
    /**
     * @Given /^there are no ([^"]*)$/
     */
    public function thereAreNoResources($type)
    {
        $type = str_replace(' ', '_', StringUtil::singularify($type));
        $type = is_array($type) ? $type[1] : $type; // Hacky hack for multiple singular forms.
        $type = $type == 'addresse' ? 'address' : $type; // Hacky hack again because we do not retrieve the right singular with the previous hack...

        $manager = $this->getEntityManager();

        foreach ($this->getRepository($type)->findAll() as $resource) {
            $manager->remove($resource);
        }

        $manager->flush();
    }

    /**
     * @When /^I wait (\d+) seconds$/
     */
    public function iWaitSeconds($arg1)
    {
        sleep($arg1);
    }

    /**
     * @Given /^([^""]*) with following data should be created:$/
     */
    public function objectWithFollowingDataShouldBeCreated($type, TableNode $table)
    {
        $data = $table->getRowsHash();
        $type = str_replace(' ', '_', trim($type));

        $object = $this->findOneByName($type, $data['name']);
        foreach ($data as $property => $value) {
            $objectValue = $object->{'get'.ucfirst($property)}();
            if (is_array($objectValue)) {
                $objectValue = implode(',', $objectValue);
            }

            if ($objectValue !== $value) {
                throw new \Exception(sprintf('%s object::%s has "%s" value but "%s" expected', $type, $property, $objectValue, is_array($value) ? implode(',', $value) : $value));
            }
        }
    }

    /**
     * @When I wait for elastic search :status_name status
     */
    public function iWaitForElasticSearchStatus($status_name)
    {
        $path = sprintf('_cluster/health?wait_for_status=%s&timeout=%s', $status_name, '15s');

        $response = $this->getService('fos_elastica.client')
            ->request($path, \Elastica\Request::GET);
    }

    /**
     * @Given /^I have deleted the ([^"]*) "([^""]*)"/
     */
    public function haveDeleted($resource, $name)
    {
        $manager = $this->getEntityManager();
        $manager->remove($this->findOneByName($resource, $name));
        $manager->flush();
    }
}

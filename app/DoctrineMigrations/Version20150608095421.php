<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150608095421 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE organization ADD created DATETIME NOT NULL, ADD updated DATETIME NOT NULL');
    }

    public function postUp(Schema $schema) {
        parent::postUp($schema);
        $this->initOrganizationCreatedAndUpdatedDates();
    }

    protected function initOrganizationCreatedAndUpdatedDates() {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        // fetch organization user created date to init organisation created date
        $query = $em
            ->createQuery('select o, u from ServiceCiviqueCoreBundle:Organization o join o.user u')
            ->useQueryCache(false);

        $batchSize = 100;
        $i = 0;
        $iterableResult = $query->iterate();

        foreach ($iterableResult as $row) {
            $organization = $row[0];
            $user = $organization->getUser();

            if(!$user) {
                continue;
            }

            $created = $user->getCreated();

            $organization->setCreated($created);
            $organization->setUpdated($created);

            $em->merge($organization);

            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear();
            }

            $i++;
        }

        $em->flush();
        $em->clear();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE organization DROP created, DROP updated');
    }
}

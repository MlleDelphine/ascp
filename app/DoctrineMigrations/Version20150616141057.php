<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150616141057 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE missions_taxons');
        $this->addSql('ALTER TABLE user ADD subscription_referer INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE missions_taxons (mission_id INT NOT NULL, taxon_id INT NOT NULL, INDEX IDX_9C87D69FBE6CAE90 (mission_id), INDEX IDX_9C87D69FDE13F470 (taxon_id), PRIMARY KEY(mission_id, taxon_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE missions_taxons ADD CONSTRAINT FK_9C87D69FBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE missions_taxons ADD CONSTRAINT FK_9C87D69FDE13F470 FOREIGN KEY (taxon_id) REFERENCES taxon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP subscription_referer');
    }
}

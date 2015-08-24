<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150609152704 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application CHANGE status status SMALLINT UNSIGNED NOT NULL, CHANGE mission_status mission_status SMALLINT UNSIGNED DEFAULT NULL, CHANGE is_preview is_preview INT UNSIGNED DEFAULT NULL');
        $this->addSql('CREATE INDEX status_idx ON application (status)');
        $this->addSql('ALTER TABLE mission CHANGE duration duration SMALLINT NOT NULL, CHANGE status status SMALLINT UNSIGNED NOT NULL, CHANGE vacancies vacancies SMALLINT UNSIGNED NOT NULL, CHANGE application_count application_count INT UNSIGNED NOT NULL');
        $this->addSql('CREATE INDEX status_idx ON mission (status)');
        $this->addSql('ALTER TABLE profile CHANGE has_profile_visited has_profile_visited TINYINT(1) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX status_idx ON application');
        $this->addSql('ALTER TABLE application CHANGE status status INT NOT NULL, CHANGE mission_status mission_status INT DEFAULT NULL, CHANGE is_preview is_preview INT DEFAULT NULL');
        $this->addSql('DROP INDEX status_idx ON mission');
        $this->addSql('ALTER TABLE mission CHANGE duration duration INT NOT NULL, CHANGE status status INT NOT NULL, CHANGE vacancies vacancies INT NOT NULL, CHANGE application_count application_count INT NOT NULL');
        $this->addSql('ALTER TABLE profile CHANGE has_profile_visited has_profile_visited INT DEFAULT NULL');
    }
}

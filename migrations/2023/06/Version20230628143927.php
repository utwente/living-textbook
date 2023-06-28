<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230628143927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE data_additional_resources (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE concept ADD additional_resources_id INT NOT NULL, CHANGE image_path image_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A6050D0101402 FOREIGN KEY (additional_resources_id) REFERENCES data_additional_resources (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E74A6050D0101402 ON concept (additional_resources_id)');
        $this->addSql('ALTER TABLE study_area_field_configuration ADD concept_additional_resources_name VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A6050D0101402');
        $this->addSql('DROP TABLE data_additional_resources');
        $this->addSql('ALTER TABLE study_area_field_configuration DROP concept_additional_resources_name');
        $this->addSql('DROP INDEX UNIQ_E74A6050D0101402 ON concept');
        $this->addSql('ALTER TABLE concept DROP additional_resources_id, CHANGE image_path image_path VARCHAR(512) DEFAULT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}

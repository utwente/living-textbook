<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211016143653 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE concept CHANGE modelCfg modelCfg LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE concept_relation CHANGE modelCfg modelCfg LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE study_area ADD modelCfg LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_document)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE concept CHANGE modelCfg modelCfg LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE concept_relation CHANGE modelCfg modelCfg LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE study_area DROP modelCfg');
    }
}

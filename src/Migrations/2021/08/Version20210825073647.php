<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210825073647 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE concept ADD modelCfg LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE concept_relation ADD modelCfg LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE study_area_group ADD is_dotron TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE concept DROP modelCfg');
        $this->addSql('ALTER TABLE concept_relation DROP modelCfg');
        $this->addSql('ALTER TABLE study_area_group DROP is_dotron');
    }
}

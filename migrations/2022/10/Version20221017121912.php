<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221017121912 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add relation styling overrides';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('CREATE TABLE styling_configuration_relation_override (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, relation_id INT DEFAULT NULL, styling_configuration_id INT DEFAULT NULL, override LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_C6F3D8E1881ABDFE (study_area_id), INDEX IDX_C6F3D8E13256915B (relation_id), INDEX IDX_C6F3D8E176A10C4A (styling_configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    $this->addSql('ALTER TABLE styling_configuration_relation_override ADD CONSTRAINT FK_C6F3D8E1881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE styling_configuration_relation_override ADD CONSTRAINT FK_C6F3D8E13256915B FOREIGN KEY (relation_id) REFERENCES concept_relation (id)');
    $this->addSql('ALTER TABLE styling_configuration_relation_override ADD CONSTRAINT FK_C6F3D8E176A10C4A FOREIGN KEY (styling_configuration_id) REFERENCES styling_configuration (id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE styling_configuration_relation_override DROP FOREIGN KEY FK_C6F3D8E1881ABDFE');
    $this->addSql('ALTER TABLE styling_configuration_relation_override DROP FOREIGN KEY FK_C6F3D8E13256915B');
    $this->addSql('ALTER TABLE styling_configuration_relation_override DROP FOREIGN KEY FK_C6F3D8E176A10C4A');
    $this->addSql('DROP TABLE styling_configuration_relation_override');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

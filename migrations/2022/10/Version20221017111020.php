<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221017111020 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add concept styling overrides';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE styling_configuration_concept_override (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, concept_id INT DEFAULT NULL, styling_configuration_id INT DEFAULT NULL, override LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_8C8C658881ABDFE (study_area_id), INDEX IDX_8C8C658F909284E (concept_id), INDEX IDX_8C8C65876A10C4A (styling_configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    $this->addSql('ALTER TABLE styling_configuration_concept_override ADD CONSTRAINT FK_8C8C658881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE styling_configuration_concept_override ADD CONSTRAINT FK_8C8C658F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE styling_configuration_concept_override ADD CONSTRAINT FK_8C8C65876A10C4A FOREIGN KEY (styling_configuration_id) REFERENCES styling_configuration (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE styling_configuration_concept_override DROP FOREIGN KEY FK_8C8C658881ABDFE');
    $this->addSql('ALTER TABLE styling_configuration_concept_override DROP FOREIGN KEY FK_8C8C658F909284E');
    $this->addSql('ALTER TABLE styling_configuration_concept_override DROP FOREIGN KEY FK_8C8C65876A10C4A');
    $this->addSql('DROP TABLE styling_configuration_concept_override');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

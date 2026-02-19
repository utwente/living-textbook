<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220919112321 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add layout configuration overrides';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE layout_configuration_override (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, concept_id INT DEFAULT NULL, layout_configuration_id INT DEFAULT NULL, override LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_D825CAE4881ABDFE (study_area_id), INDEX IDX_D825CAE4F909284E (concept_id), INDEX IDX_D825CAE476B4B09F (layout_configuration_id), UNIQUE INDEX concept_layout_join (concept_id, layout_configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    $this->addSql('ALTER TABLE layout_configuration_override ADD CONSTRAINT FK_D825CAE4881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE layout_configuration_override ADD CONSTRAINT FK_D825CAE4F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE layout_configuration_override ADD CONSTRAINT FK_D825CAE476B4B09F FOREIGN KEY (layout_configuration_id) REFERENCES layout_configuration (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE layout_configuration_override DROP FOREIGN KEY FK_D825CAE4881ABDFE');
    $this->addSql('ALTER TABLE layout_configuration_override DROP FOREIGN KEY FK_D825CAE4F909284E');
    $this->addSql('ALTER TABLE layout_configuration_override DROP FOREIGN KEY FK_D825CAE476B4B09F');
    $this->addSql('DROP TABLE layout_configuration_override');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

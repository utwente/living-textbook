<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261106114941 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add deprecated skill setting';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD deprecated_skills TINYINT DEFAULT 0 NOT NULL AFTER analytics_dashboard_enabled');
    $this->addSql('UPDATE study_area SET deprecated_skills = 1');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP deprecated_skills');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

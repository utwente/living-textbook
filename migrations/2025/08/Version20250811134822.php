<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250811134822 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Export studyarea to a given url';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD url_export_enabled TINYINT(1) NOT NULL');
    $this->addSql('ALTER TABLE study_area ADD export_url VARCHAR(512) DEFAULT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP url_export_enabled');
    $this->addSql('ALTER TABLE study_area DROP export_url');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

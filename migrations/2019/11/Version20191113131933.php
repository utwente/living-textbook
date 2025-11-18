<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191113131933 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added analytics dashboard to study area';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD analytics_dashboard_enabled TINYINT(1) NOT NULL AFTER open_access');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP analytics_dashboard_enabled');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

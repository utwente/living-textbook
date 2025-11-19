<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211223124234 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add api enabled option to study area';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area
    ADD api_enabled TINYINT(1) DEFAULT \'0\' NOT NULL AFTER review_mode_enabled,
    CHANGE open_access open_access TINYINT(1) DEFAULT \'0\' NOT NULL,
    CHANGE analytics_dashboard_enabled analytics_dashboard_enabled TINYINT(1) DEFAULT \'0\' NOT NULL,
    CHANGE review_mode_enabled review_mode_enabled TINYINT(1) DEFAULT \'0\' NOT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP api_enabled, CHANGE open_access open_access TINYINT(1) NOT NULL, CHANGE analytics_dashboard_enabled analytics_dashboard_enabled TINYINT(1) NOT NULL, CHANGE review_mode_enabled review_mode_enabled TINYINT(1) NOT NULL');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

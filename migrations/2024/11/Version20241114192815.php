<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241114192815 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add canvas configuration';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD map_width INT DEFAULT 3000 NOT NULL AFTER api_enabled, ADD map_height INT DEFAULT 2000 NOT NULL AFTER map_width');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP map_width, DROP map_height');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

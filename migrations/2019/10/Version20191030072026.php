<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191030072026 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added review mode toggle to study area';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD review_mode_enabled TINYINT(1) NOT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP review_mode_enabled');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190414141749 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Track user setting study area';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD track_users TINYINT(1) NOT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP track_users');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

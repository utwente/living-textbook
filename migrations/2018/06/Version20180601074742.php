<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180601074742 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE external_resource CHANGE url url TEXT DEFAULT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE external_resource CHANGE url url TEXT NOT NULL COLLATE utf8mb4_unicode_ci');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

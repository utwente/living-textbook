<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180803134435 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE user__table CHANGE last_used last_used DATETIME DEFAULT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE user__table CHANGE last_used last_used DATETIME NOT NULL');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

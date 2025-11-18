<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180518133122 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE user__table ADD is_admin TINYINT(1) NOT NULL AFTER is_active');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE user__table DROP is_admin');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

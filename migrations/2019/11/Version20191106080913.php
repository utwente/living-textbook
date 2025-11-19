<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191106080913 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added review notes field';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE review ADD notes LONGTEXT DEFAULT NULL AFTER owner_id');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE review DROP notes');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

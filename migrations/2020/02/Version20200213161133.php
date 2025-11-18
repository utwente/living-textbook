<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200213161133 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add email field to contributor';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE contributor ADD email VARCHAR(255) DEFAULT NULL AFTER url');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE contributor DROP email');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

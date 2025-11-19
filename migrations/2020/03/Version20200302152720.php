<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200302152720 extends AbstractMigration
{
  public function getDescription(): string
  {
    return '';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE user__table ADD reset_code VARCHAR(255) DEFAULT NULL, ADD reset_code_valid DATETIME DEFAULT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE user__table DROP reset_code, DROP reset_code_valid');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

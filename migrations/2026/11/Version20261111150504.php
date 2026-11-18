<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261111150504 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add drenso deployer scripts table';
  }

  public function up(Schema $schema): void
  {
    $this->addSql(<<<'SQL'
            CREATE TABLE _drenso_deployer_scripts (
              script VARCHAR(255) NOT NULL,
              executed_at DATETIME NOT NULL,
              PRIMARY KEY (script)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE _drenso_deployer_scripts');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

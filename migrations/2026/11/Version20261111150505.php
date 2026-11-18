<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261111150505 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Update external resources to allow null value for title';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE external_resource CHANGE title title VARCHAR(512) DEFAULT NULL');
    $this->addSql('UPDATE external_resource SET title = NULL WHERE title = \'\'');
    $this->addSql('UPDATE external_resource SET description = NULL WHERE description = \'\'');
    $this->addSql('UPDATE external_resource SET url = NULL WHERE url = \'\'');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE external_resource CHANGE title title VARCHAR(512) NOT NULL');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

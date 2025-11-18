<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190531092106 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added annotation visibility';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE annotation ADD visibility VARCHAR(10) NULL AFTER concept_id');
    $this->addSql('UPDATE annotation SET visibility = "private" WHERE visibility IS NULL');
    $this->addSql('ALTER TABLE annotation CHANGE visibility visibility VARCHAR(10) NOT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE annotation DROP visibility');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

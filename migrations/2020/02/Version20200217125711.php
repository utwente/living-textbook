<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200217125711 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added instance field to concept';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE concept ADD instance TINYINT(1) NOT NULL AFTER name');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE concept DROP instance');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190322122549 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Splitted definition and introduction fields';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE concept ADD definition LONGTEXT NOT NULL AFTER name');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE concept DROP definition');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

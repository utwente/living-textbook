<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220225110031 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Modify dotron properties';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD dotron TINYINT(1) NOT NULL');
    $this->addSql('ALTER TABLE study_area_group DROP dotron');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP dotron');
    $this->addSql('ALTER TABLE study_area_group ADD dotron TINYINT(1) NOT NULL');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

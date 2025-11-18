<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190127153327 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD frozen_on DATETIME DEFAULT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP frozen_on');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

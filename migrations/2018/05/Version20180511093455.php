<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180511093455 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD access_type VARCHAR(10) NOT NULL');
    $this->addSql('UPDATE study_area SET access_type = "public"');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP access_type');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190930153122 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add open access field';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD open_access TINYINT(1) NOT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP open_access');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

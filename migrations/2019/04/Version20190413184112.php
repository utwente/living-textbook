<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190413184112 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added print header and introduction';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area ADD print_header VARCHAR(100) DEFAULT NULL, ADD print_introduction LONGTEXT DEFAULT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP print_header, DROP print_introduction');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

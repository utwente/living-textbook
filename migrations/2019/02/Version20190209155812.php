<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190209155812 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add description to relation types';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE relation_type ADD description LONGTEXT DEFAULT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE relation_type DROP description');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

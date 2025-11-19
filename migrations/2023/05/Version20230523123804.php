<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230523123804 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'adding description attribute to tag';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE tag ADD description LONGTEXT DEFAULT NULL AFTER color');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE tag DROP description');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190414155706 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Purge all tracking data before opt-in implementation';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('TRUNCATE TABLE page_load');
  }

  public function down(Schema $schema): void
  {
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

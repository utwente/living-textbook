<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180708093948 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('UPDATE study_area SET access_type = "private" WHERE access_type = "individual"');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('UPDATE study_area SET access_type = "individual" WHERE access_type = "private"');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

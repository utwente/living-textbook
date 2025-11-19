<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251119205108 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Drop comments from messenger queue table';
  }

  public function up(Schema $schema): void
  {
    $this->addSql(<<<SQL
      ALTER TABLE _messenger_queue
        CHANGE queue_name queue_name VARCHAR(190) NOT NULL,
        CHANGE created_at created_at DATETIME NOT NULL,
        CHANGE available_at available_at DATETIME NOT NULL,
        CHANGE delivered_at delivered_at DATETIME DEFAULT NULL
SQL,
    );
  }

  public function down(Schema $schema): void
  {
    $this->addSql(<<<SQL
      ALTER TABLE _messenger_queue
        CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
        CHANGE available_at available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
        CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
SQL,
    );
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

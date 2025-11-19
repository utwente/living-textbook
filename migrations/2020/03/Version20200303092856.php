<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200303092856 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add UserProto entity';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE user_proto (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, invited_at DATETIME NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C9D1C8BB35C246D5 (password), INDEX IDX_C9D1C8BBE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE user_proto');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

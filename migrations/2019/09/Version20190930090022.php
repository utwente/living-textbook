<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use DateTime;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190930090022 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add help page';
  }

  public function up(Schema $schema): void
  {
    $now = new DateTime();

    $this->addSql('CREATE TABLE help (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('INSERT INTO help (content, created_at, created_by) VALUES ("", "' . $now->format('Y-m-d H:i:s') . '", "system")');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE help');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180816191537 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE external_resource CHANGE title title VARCHAR(512) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE url url VARCHAR(512) DEFAULT NULL');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE external_resource CHANGE title title TINYTEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE description description TEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE url url TEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

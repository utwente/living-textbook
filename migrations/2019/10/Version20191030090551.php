<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191030090551 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added pending change entity';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE pending_change (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, change_type VARCHAR(10) NOT NULL, object_type VARCHAR(255) NOT NULL, object_id INT DEFAULT NULL, payload LONGTEXT NOT NULL, changed_fields LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, INDEX IDX_4E13E390881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE pending_change ADD CONSTRAINT FK_4E13E390881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE pending_change');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

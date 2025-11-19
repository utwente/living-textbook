<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180719182901 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE abbreviation (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, abbreviation VARCHAR(25) NOT NULL, meaning VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_BCF3411D881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE abbreviation ADD CONSTRAINT FK_BCF3411D881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE abbreviation');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

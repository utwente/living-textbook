<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190104084013 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE learning_path (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, name VARCHAR(255) NOT NULL, question VARCHAR(1024) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_4D04C797881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE learning_path ADD CONSTRAINT FK_4D04C797881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE learning_path');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220512133707 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Adding styling configuration';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE styling_configuration (
          id INT AUTO_INCREMENT NOT NULL,
          study_area_id INT NOT NULL,
          stylings LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\',
          created_at DATETIME NOT NULL,
          created_by VARCHAR(255) DEFAULT NULL,
          updated_at DATETIME DEFAULT NULL,
          updated_by VARCHAR(255) DEFAULT NULL,
          deleted_at DATETIME DEFAULT NULL,
          deleted_by VARCHAR(255) DEFAULT NULL,
          INDEX IDX_C7E17E45881ABDFE (study_area_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    $this->addSql('ALTER TABLE styling_configuration ADD CONSTRAINT FK_C7E17E45881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE styling_configuration');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

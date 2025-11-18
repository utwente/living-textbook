<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190329162430 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added page load table';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE page_load (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, user_id VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, session_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', path VARCHAR(1024) NOT NULL, path_context LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', origin VARCHAR(1024) DEFAULT NULL, origin_context LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_A2C2F8C7881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE page_load ADD CONSTRAINT FK_A2C2F8C7881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE page_load');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

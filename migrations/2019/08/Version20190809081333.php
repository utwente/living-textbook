<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190809081333 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added study area group';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE study_area_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE study_area ADD group_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD551FE54D947 FOREIGN KEY (group_id) REFERENCES study_area_group (id)');
    $this->addSql('CREATE INDEX IDX_A84CD551FE54D947 ON study_area (group_id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD551FE54D947');
    $this->addSql('DROP TABLE study_area_group');
    $this->addSql('DROP INDEX IDX_A84CD551FE54D947 ON study_area');
    $this->addSql('ALTER TABLE study_area DROP group_id');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

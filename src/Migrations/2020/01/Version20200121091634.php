<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200121091634 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added contributor';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE contributor (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, name VARCHAR(512) NOT NULL, description LONGTEXT DEFAULT NULL, url VARCHAR(512) DEFAULT NULL, broken TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_DA6F9793881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE concepts_contributors (concept_id INT NOT NULL, contributor_id INT NOT NULL, INDEX IDX_9DEA5166F909284E (concept_id), INDEX IDX_9DEA51667A19A357 (contributor_id), PRIMARY KEY(concept_id, contributor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE contributor ADD CONSTRAINT FK_DA6F9793881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE concepts_contributors ADD CONSTRAINT FK_9DEA5166F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE concepts_contributors ADD CONSTRAINT FK_9DEA51667A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE concepts_contributors');
    $this->addSql('DROP TABLE contributor');
  }
}

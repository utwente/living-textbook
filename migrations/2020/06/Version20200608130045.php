<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200608130045 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add tag table';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, name VARCHAR(25) NOT NULL, color VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_389B783881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    $this->addSql('CREATE TABLE concept_tag (concept_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_F9C0BAAFF909284E (concept_id), INDEX IDX_F9C0BAAFBAD26311 (tag_id), PRIMARY KEY(concept_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE concept_tag ADD CONSTRAINT FK_F9C0BAAFF909284E FOREIGN KEY (concept_id) REFERENCES concept (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE concept_tag ADD CONSTRAINT FK_F9C0BAAFBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE concept_tag DROP FOREIGN KEY FK_F9C0BAAFBAD26311');
    $this->addSql('DROP TABLE tag');
    $this->addSql('DROP TABLE concept_tag');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

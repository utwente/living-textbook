<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180508124001 extends AbstractMigration
{
  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE learning_outcome (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, number INT NOT NULL, name VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_F41BD531881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE concepts_learning_outcomes (concept_id INT NOT NULL, learning_outcome_id INT NOT NULL, INDEX IDX_AEA47E60F909284E (concept_id), INDEX IDX_AEA47E6035C2B2D5 (learning_outcome_id), PRIMARY KEY(concept_id, learning_outcome_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE learning_outcome ADD CONSTRAINT FK_F41BD531881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE concepts_learning_outcomes ADD CONSTRAINT FK_AEA47E60F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE concepts_learning_outcomes ADD CONSTRAINT FK_AEA47E6035C2B2D5 FOREIGN KEY (learning_outcome_id) REFERENCES learning_outcome (id)');
    $this->addSql('DROP TABLE data_learning_outcomes');
    $this->addSql('ALTER TABLE concept DROP learning_outcomes_id');
  }

  public function down(Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE concepts_learning_outcomes DROP FOREIGN KEY FK_AEA47E6035C2B2D5');
    $this->addSql('CREATE TABLE data_learning_outcomes (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('DROP TABLE learning_outcome');
    $this->addSql('DROP TABLE concepts_learning_outcomes');
    $this->addSql('ALTER TABLE concept ADD learning_outcomes_id INT NOT NULL');
  }
}

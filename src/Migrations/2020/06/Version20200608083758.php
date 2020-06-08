<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200608083758 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add field configuration entity';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE study_area_field_configuration (id INT AUTO_INCREMENT NOT NULL, concept_definition_name VARCHAR(50) DEFAULT NULL, concept_introduction_name VARCHAR(50) DEFAULT NULL, concept_synonyms_name VARCHAR(50) DEFAULT NULL, concept_prior_knowledge_name VARCHAR(50) DEFAULT NULL, concept_theory_explanation_name VARCHAR(50) DEFAULT NULL, concept_howto_name VARCHAR(50) DEFAULT NULL, concept_examples_name VARCHAR(50) DEFAULT NULL, concept_self_assessment_name VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    $this->addSql('ALTER TABLE study_area ADD field_configuration_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD551317DA47B FOREIGN KEY (field_configuration_id) REFERENCES study_area_field_configuration (id)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_A84CD551317DA47B ON study_area (field_configuration_id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD551317DA47B');
    $this->addSql('DROP TABLE study_area_field_configuration');
    $this->addSql('DROP INDEX UNIQ_A84CD551317DA47B ON study_area');
    $this->addSql('ALTER TABLE study_area DROP field_configuration_id');
  }
}

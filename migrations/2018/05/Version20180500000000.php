<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180500000000 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE study_area (id INT AUTO_INCREMENT NOT NULL, owner_user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_A84CD5512B18554A (owner_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE external_resource (id INT AUTO_INCREMENT NOT NULL, collection_id INT NOT NULL, title TINYTEXT NOT NULL, description TEXT NOT NULL, url TEXT NOT NULL, position INT NOT NULL, broken TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_539D46E5514956FD (collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation_type (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_3BF454A4881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE data_theory_explanation (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE data_examples (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE data_external_resources (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE data_how_to (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE data_introduction (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE data_self_assessment (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE user__table (id INT AUTO_INCREMENT NOT NULL, given_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, full_name VARCHAR(200) NOT NULL, display_name VARCHAR(200) NOT NULL, username VARCHAR(255) NOT NULL, is_oidc TINYINT(1) NOT NULL, password VARCHAR(255) DEFAULT NULL, registered_on DATETIME NOT NULL, last_used DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_194378E7F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE learning_outcome (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, number INT NOT NULL, name VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_F41BD531881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE concept_relation (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, target_id INT NOT NULL, relation_type INT NOT NULL, outgoing_position INT NOT NULL, incoming_position INT NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_5CAE2E9953C1C61 (source_id), INDEX IDX_5CAE2E9158E0B66 (target_id), INDEX IDX_5CAE2E93BF454A4 (relation_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE concept (id INT AUTO_INCREMENT NOT NULL, introduction_id INT NOT NULL, theory_explanation_id INT NOT NULL, how_to_id INT NOT NULL, examples_id INT NOT NULL, external_resources_id INT NOT NULL, self_assessment_id INT NOT NULL, study_area_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E74A605087D2B3A9 (introduction_id), UNIQUE INDEX UNIQ_E74A6050AB56AAE7 (theory_explanation_id), UNIQUE INDEX UNIQ_E74A60502A796CFA (how_to_id), UNIQUE INDEX UNIQ_E74A60508D6A9A10 (examples_id), UNIQUE INDEX UNIQ_E74A605062F5782D (external_resources_id), UNIQUE INDEX UNIQ_E74A60509ECF1EA3 (self_assessment_id), INDEX IDX_E74A6050881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE concepts_prior_knowledge (concept_id INT NOT NULL, prior_knowledge_id INT NOT NULL, INDEX IDX_4411F4B5F909284E (concept_id), INDEX IDX_4411F4B555853A57 (prior_knowledge_id), PRIMARY KEY(concept_id, prior_knowledge_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE concepts_learning_outcomes (concept_id INT NOT NULL, learning_outcome_id INT NOT NULL, INDEX IDX_AEA47E60F909284E (concept_id), INDEX IDX_AEA47E6035C2B2D5 (learning_outcome_id), PRIMARY KEY(concept_id, learning_outcome_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD5512B18554A FOREIGN KEY (owner_user_id) REFERENCES user__table (id)');
    $this->addSql('ALTER TABLE external_resource ADD CONSTRAINT FK_539D46E5514956FD FOREIGN KEY (collection_id) REFERENCES data_external_resources (id)');
    $this->addSql('ALTER TABLE relation_type ADD CONSTRAINT FK_3BF454A4881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE learning_outcome ADD CONSTRAINT FK_F41BD531881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE concept_relation ADD CONSTRAINT FK_5CAE2E9953C1C61 FOREIGN KEY (source_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE concept_relation ADD CONSTRAINT FK_5CAE2E9158E0B66 FOREIGN KEY (target_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE concept_relation ADD CONSTRAINT FK_5CAE2E93BF454A4 FOREIGN KEY (relation_type) REFERENCES relation_type (id)');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A605087D2B3A9 FOREIGN KEY (introduction_id) REFERENCES data_introduction (id)');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A6050AB56AAE7 FOREIGN KEY (theory_explanation_id) REFERENCES data_theory_explanation (id)');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A60502A796CFA FOREIGN KEY (how_to_id) REFERENCES data_how_to (id)');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A60508D6A9A10 FOREIGN KEY (examples_id) REFERENCES data_examples (id)');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A605062F5782D FOREIGN KEY (external_resources_id) REFERENCES data_external_resources (id)');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A60509ECF1EA3 FOREIGN KEY (self_assessment_id) REFERENCES data_self_assessment (id)');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A6050881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE concepts_prior_knowledge ADD CONSTRAINT FK_4411F4B5F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE concepts_prior_knowledge ADD CONSTRAINT FK_4411F4B555853A57 FOREIGN KEY (prior_knowledge_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE concepts_learning_outcomes ADD CONSTRAINT FK_AEA47E60F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE concepts_learning_outcomes ADD CONSTRAINT FK_AEA47E6035C2B2D5 FOREIGN KEY (learning_outcome_id) REFERENCES learning_outcome (id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation_type DROP FOREIGN KEY FK_3BF454A4881ABDFE');
    $this->addSql('ALTER TABLE learning_outcome DROP FOREIGN KEY FK_F41BD531881ABDFE');
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A6050881ABDFE');
    $this->addSql('ALTER TABLE concept_relation DROP FOREIGN KEY FK_5CAE2E93BF454A4');
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A6050AB56AAE7');
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A60508D6A9A10');
    $this->addSql('ALTER TABLE external_resource DROP FOREIGN KEY FK_539D46E5514956FD');
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A605062F5782D');
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A60502A796CFA');
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A605087D2B3A9');
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A60509ECF1EA3');
    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD5512B18554A');
    $this->addSql('ALTER TABLE concepts_learning_outcomes DROP FOREIGN KEY FK_AEA47E6035C2B2D5');
    $this->addSql('ALTER TABLE concept_relation DROP FOREIGN KEY FK_5CAE2E9953C1C61');
    $this->addSql('ALTER TABLE concept_relation DROP FOREIGN KEY FK_5CAE2E9158E0B66');
    $this->addSql('ALTER TABLE concepts_prior_knowledge DROP FOREIGN KEY FK_4411F4B5F909284E');
    $this->addSql('ALTER TABLE concepts_prior_knowledge DROP FOREIGN KEY FK_4411F4B555853A57');
    $this->addSql('ALTER TABLE concepts_learning_outcomes DROP FOREIGN KEY FK_AEA47E60F909284E');
    $this->addSql('DROP TABLE study_area');
    $this->addSql('DROP TABLE external_resource');
    $this->addSql('DROP TABLE relation_type');
    $this->addSql('DROP TABLE data_theory_explanation');
    $this->addSql('DROP TABLE data_examples');
    $this->addSql('DROP TABLE data_external_resources');
    $this->addSql('DROP TABLE data_how_to');
    $this->addSql('DROP TABLE data_introduction');
    $this->addSql('DROP TABLE data_self_assessment');
    $this->addSql('DROP TABLE user__table');
    $this->addSql('DROP TABLE learning_outcome');
    $this->addSql('DROP TABLE concept_relation');
    $this->addSql('DROP TABLE concept');
    $this->addSql('DROP TABLE concepts_prior_knowledge');
    $this->addSql('DROP TABLE concepts_learning_outcomes');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

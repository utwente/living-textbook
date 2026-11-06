<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261106111417 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add new skills';
  }

  public function up(Schema $schema): void
  {
    $this->addSql(<<<'SQL'
            CREATE TABLE data_skills (
              id INT AUTO_INCREMENT NOT NULL,
              analyse_attributing VARCHAR(255) DEFAULT NULL,
              analyse_differentiating VARCHAR(255) DEFAULT NULL,
              analyse_organising VARCHAR(255) DEFAULT NULL,
              apply_executing VARCHAR(255) DEFAULT NULL,
              apply_implementing VARCHAR(255) DEFAULT NULL,
              create_generating VARCHAR(255) DEFAULT NULL,
              create_planning VARCHAR(255) DEFAULT NULL,
              create_producing VARCHAR(255) DEFAULT NULL,
              evaluate_checking VARCHAR(255) DEFAULT NULL,
              evaluate_critiquing VARCHAR(255) DEFAULT NULL,
              remember_recalling VARCHAR(255) DEFAULT NULL,
              remember_recognising VARCHAR(255) DEFAULT NULL,
              understand_classifying VARCHAR(255) DEFAULT NULL,
              understand_comparing VARCHAR(255) DEFAULT NULL,
              understand_exemplifying VARCHAR(255) DEFAULT NULL,
              understand_explaining VARCHAR(255) DEFAULT NULL,
              understand_inferring VARCHAR(255) DEFAULT NULL,
              understand_interpreting VARCHAR(255) DEFAULT NULL,
              understand_summarising VARCHAR(255) DEFAULT NULL,
              created_at DATETIME NOT NULL,
              created_by VARCHAR(255) DEFAULT NULL,
              updated_at DATETIME DEFAULT NULL,
              updated_by VARCHAR(255) DEFAULT NULL,
              deleted_at DATETIME DEFAULT NULL,
              deleted_by VARCHAR(255) DEFAULT NULL,
              PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL,
    );

    // Generate a skill entry for every concept
    $this->addSql('ALTER TABLE concept ADD skills_id INT DEFAULT NULL AFTER self_assessment_id');
    $this->addSql('INSERT INTO data_skills (id, created_at, created_by) SELECT c.id, NOW(), "Migration" FROM concept c');
    $this->addSql('UPDATE concept c INNER JOIN data_skills ds ON c.id = ds.id SET c.skills_id = ds.id');
    $this->addSql('ALTER TABLE concept CHANGE skills_id skills_id INT NOT NULL');
    $this->addSql(<<<'SQL'
            ALTER TABLE concept
            ADD CONSTRAINT FK_E74A60507FF61858 FOREIGN KEY (skills_id) REFERENCES data_skills (id)
        SQL,
    );
    $this->addSql('CREATE UNIQUE INDEX UNIQ_E74A60507FF61858 ON concept (skills_id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A60507FF61858');
    $this->addSql('DROP INDEX UNIQ_E74A60507FF61858 ON concept');
    $this->addSql('ALTER TABLE concept DROP skills_id');
    $this->addSql('DROP TABLE data_skills');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

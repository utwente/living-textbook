<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261112192836 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Migrate studyarea configuration for (un)classified skills';
  }

  public function up(Schema $schema): void
  {
    $this->addSql(<<<'SQL'
            ALTER TABLE study_area
            CHANGE deprecated_skills learning_outcomes_enabled TINYINT DEFAULT 1 NOT NULL,
            ADD skills_enabled TINYINT DEFAULT 1 NOT NULL AFTER learning_outcomes_enabled
        SQL);
  }

  public function down(Schema $schema): void
  {
    $this->addSql(<<<'SQL'
            ALTER TABLE study_area
            CHANGE learning_outcomes_enabled deprecated_skills TINYINT DEFAULT 0 NOT NULL,
            DROP skills_enabled
        SQL);
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180816183239 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('SET foreign_key_checks = 0');
    $this->addSql('ALTER TABLE abbreviation CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE concept CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE concept_relation CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE concepts_external_resources CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE concepts_learning_outcomes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE concepts_prior_knowledge CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE data_examples CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE data_how_to CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE data_introduction CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE data_self_assessment CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE data_theory_explanation CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE external_resource CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE learning_outcome CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE relation_type CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE study_area CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE user__table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE user_group CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE user_group_email CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('ALTER TABLE user_group_users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $this->addSql('SET foreign_key_checks = 1');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs

  }

  public function isTransactional(): bool
  {
    return false;
  }
}

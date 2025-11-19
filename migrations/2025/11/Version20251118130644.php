<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Database\Migrations\ArrayToJsonMigrator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251118130644 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Updates due to Doctrine upgrade (convert last PHP arrays to JSON and remove unnecessary comments)';
  }

  public function preUp(Schema $schema): void
  {
    // Should have been nullable, never caused issue because null was still serialized to a string
    $this->connection->executeQuery('ALTER TABLE page_load CHANGE origin_context origin_context LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    ArrayToJsonMigrator::migrateUp($this->connection, 'page_load', 'path_context', $this->write(...));
    ArrayToJsonMigrator::migrateUp($this->connection, 'page_load', 'origin_context', $this->write(...));
    ArrayToJsonMigrator::migrateUp($this->connection, 'tracking_event', 'context', $this->write(...));
    ArrayToJsonMigrator::migrateUp($this->connection, 'user__table', 'roles', $this->write(...));
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE concept CHANGE dotron_config dotron_config JSON DEFAULT NULL');
    $this->addSql('ALTER TABLE concept_relation CHANGE dotron_config dotron_config JSON DEFAULT NULL');
    $this->addSql('ALTER TABLE page_load CHANGE session_id session_id CHAR(36) NOT NULL, CHANGE path_context path_context JSON DEFAULT NULL, CHANGE origin_context origin_context JSON DEFAULT NULL');
    $this->addSql('ALTER TABLE pending_change CHANGE changed_fields changed_fields JSON NOT NULL, CHANGE review_comments review_comments JSON DEFAULT NULL');
    $this->addSql('ALTER TABLE CHANGE url_export_enabled url_export_enabled TINYINT(1) DEFAULT 0 NOT NULL');
    $this->addSql('ALTER TABLE styling_configuration CHANGE stylings stylings JSON DEFAULT NULL');
    $this->addSql('ALTER TABLE tracking_event CHANGE session_id session_id CHAR(36) NOT NULL, CHANGE context context JSON DEFAULT NULL');
    $this->addSql('ALTER TABLE user__table CHANGE roles roles JSON NOT NULL');
    $this->addSql('ALTER TABLE user_api_token CHANGE valid_until valid_until DATETIME DEFAULT NULL, CHANGE last_used last_used DATETIME DEFAULT NULL');
    $this->addSql('ALTER TABLE user_browser_state CHANGE filter_state filter_state JSON DEFAULT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE concept CHANGE dotron_config dotron_config JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE concept_relation CHANGE dotron_config dotron_config JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE page_load CHANGE session_id session_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE path_context path_context LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE origin_context origin_context LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    $this->addSql('ALTER TABLE pending_change CHANGE changed_fields changed_fields JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE review_comments review_comments JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE study_area CHANGE dotron_config dotron_config JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE url_export_enabled url_export_enabled TINYINT(1) NOT NULL');
    $this->addSql('ALTER TABLE styling_configuration CHANGE stylings stylings JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE tracking_event CHANGE session_id session_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE context context LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    $this->addSql('ALTER TABLE user_api_token CHANGE valid_until valid_until DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE last_used last_used DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    $this->addSql('ALTER TABLE user_browser_state CHANGE filter_state filter_state JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE user__table CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
  }

  public function postDown(Schema $schema): void
  {
    ArrayToJsonMigrator::migrateDown($this->connection, 'page_load', 'path_context', $this->write(...));
    ArrayToJsonMigrator::migrateDown($this->connection, 'page_load', 'origin_context', $this->write(...));
    ArrayToJsonMigrator::migrateDown($this->connection, 'tracking_event', 'context', $this->write(...));
    ArrayToJsonMigrator::migrateDown($this->connection, 'user__table', 'roles', $this->write(...));
    $this->connection->executeQuery('ALTER TABLE page_load CHANGE origin_context origin_context LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

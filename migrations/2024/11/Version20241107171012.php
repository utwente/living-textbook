<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241107171012 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Update database for new db version';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE concept CHANGE dotron_config dotron_config JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE concept_relation CHANGE dotron_config dotron_config JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE pending_change
        CHANGE changed_fields changed_fields JSON NOT NULL COMMENT \'(DC2Type:json)\',
        CHANGE review_comments review_comments JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE styling_configuration CHANGE stylings stylings JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE user_browser_state CHANGE filter_state filter_state JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE user_browser_state CHANGE filter_state filter_state JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE styling_configuration CHANGE stylings stylings JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE pending_change
        CHANGE changed_fields changed_fields JSON NOT NULL COMMENT \'(DC2Type:json)\',
        CHANGE review_comments review_comments JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE concept CHANGE dotron_config dotron_config JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE concept_relation CHANGE dotron_config dotron_config JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

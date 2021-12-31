<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191108083039 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Move review comments to pending change';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE review DROP review_comments');
    $this->addSql('ALTER TABLE pending_change ADD review_comments LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\' AFTER review_id');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE pending_change DROP review_comments');
    $this->addSql('ALTER TABLE review ADD review_comments LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:json)\'');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

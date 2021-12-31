<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191104105632 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added review entity, moved properties from pending change to it';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, owner_id INT NOT NULL, requested_review_by_id INT NOT NULL, requested_review_at DATETIME NOT NULL, review_comments LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, INDEX IDX_794381C6881ABDFE (study_area_id), INDEX IDX_794381C67E3C61F9 (owner_id), INDEX IDX_794381C65872306B (requested_review_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C67E3C61F9 FOREIGN KEY (owner_id) REFERENCES user__table (id)');
    $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C65872306B FOREIGN KEY (requested_review_by_id) REFERENCES user__table (id)');
    $this->addSql('ALTER TABLE pending_change DROP FOREIGN KEY FK_4E13E3905872306B');
    $this->addSql('DROP INDEX IDX_4E13E3905872306B ON pending_change');
    $this->addSql('ALTER TABLE pending_change DROP requested_review_at, DROP review_comments, DROP requested_review_by_id, ADD review_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE pending_change ADD CONSTRAINT FK_4E13E3903E2E969B FOREIGN KEY (review_id) REFERENCES review (id)');
    $this->addSql('CREATE INDEX IDX_4E13E3903E2E969B ON pending_change (review_id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE pending_change DROP FOREIGN KEY FK_4E13E3903E2E969B');
    $this->addSql('DROP TABLE review');
    $this->addSql('DROP INDEX IDX_4E13E3903E2E969B ON pending_change');
    $this->addSql('ALTER TABLE pending_change ADD requested_review_at DATETIME DEFAULT NULL, ADD review_comments LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:json)\', DROP review_id, ADD requested_review_by_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE pending_change ADD CONSTRAINT FK_4E13E3905872306B FOREIGN KEY (requested_review_by_id) REFERENCES user__table (id)');
    $this->addSql('CREATE INDEX IDX_4E13E3905872306B ON pending_change (requested_review_by_id)');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

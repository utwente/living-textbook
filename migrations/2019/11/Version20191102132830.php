<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191102132830 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added review information to the pending change entity';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE pending_change ADD owner_id INT NOT NULL, ADD requested_review_by_id INT DEFAULT NULL, ADD requested_review_at DATETIME DEFAULT NULL, ADD review_comments LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE pending_change ADD CONSTRAINT FK_4E13E3907E3C61F9 FOREIGN KEY (owner_id) REFERENCES user__table (id)');
    $this->addSql('ALTER TABLE pending_change ADD CONSTRAINT FK_4E13E3905872306B FOREIGN KEY (requested_review_by_id) REFERENCES user__table (id)');
    $this->addSql('CREATE INDEX IDX_4E13E3907E3C61F9 ON pending_change (owner_id)');
    $this->addSql('CREATE INDEX IDX_4E13E3905872306B ON pending_change (requested_review_by_id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE pending_change DROP FOREIGN KEY FK_4E13E3907E3C61F9');
    $this->addSql('ALTER TABLE pending_change DROP FOREIGN KEY FK_4E13E3905872306B');
    $this->addSql('DROP INDEX IDX_4E13E3907E3C61F9 ON pending_change');
    $this->addSql('DROP INDEX IDX_4E13E3905872306B ON pending_change');
    $this->addSql('ALTER TABLE pending_change DROP owner_id, DROP requested_review_by_id, DROP requested_review_at, DROP review_comments');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200212091611 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added reviewed at date';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE review ADD reviewed_by_id INT DEFAULT NULL AFTER requested_review_at');
    $this->addSql('ALTER TABLE review ADD reviewed_at DATETIME DEFAULT NULL AFTER reviewed_by_id');
    $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6FC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES user__table (id)');
    $this->addSql('CREATE INDEX IDX_794381C6FC6B21F1 ON review (reviewed_by_id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE review DROP reviewed_at');
    $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6FC6B21F1');
    $this->addSql('DROP INDEX IDX_794381C6FC6B21F1 ON review');
    $this->addSql('ALTER TABLE review DROP reviewed_by_id');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

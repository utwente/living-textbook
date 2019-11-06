<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106090103 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added approval fields';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE review ADD approved_by_id INT DEFAULT NULL AFTER review_comments, ADD approved_at DATETIME DEFAULT NULL AFTER approved_by_id');
    $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C62D234F6A FOREIGN KEY (approved_by_id) REFERENCES user__table (id)');
    $this->addSql('CREATE INDEX IDX_794381C62D234F6A ON review (approved_by_id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C62D234F6A');
    $this->addSql('DROP INDEX IDX_794381C62D234F6A ON review');
    $this->addSql('ALTER TABLE review DROP approved_by_id, DROP approved_at');
  }
}

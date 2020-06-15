<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615084851 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add user browser state entity';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE user_browser_state (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, study_area_id INT NOT NULL, filter_state LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_222668BAA76ED395 (user_id), INDEX IDX_222668BA881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    $this->addSql('ALTER TABLE user_browser_state ADD CONSTRAINT FK_222668BAA76ED395 FOREIGN KEY (user_id) REFERENCES user__table (id)');
    $this->addSql('ALTER TABLE user_browser_state ADD CONSTRAINT FK_222668BA881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_222668BAA76ED395881ABDFE ON user_browser_state (user_id, study_area_id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE user_browser_state');
  }
}

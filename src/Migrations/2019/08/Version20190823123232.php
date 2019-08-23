<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190823123232 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added tracking event';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE tracking_event (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, user_id VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, session_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', event VARCHAR(50) NOT NULL, context LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_D0F2130A881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE tracking_event ADD CONSTRAINT FK_D0F2130A881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE tracking_event');
  }
}

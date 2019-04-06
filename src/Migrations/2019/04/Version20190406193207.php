<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190406193207 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added annotation entity';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE annotation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, concept_id INT NOT NULL, text LONGTEXT DEFAULT NULL, context VARCHAR(50) NOT NULL, start INT NOT NULL, end INT NOT NULL, selected_text LONGTEXT NOT NULL, version DATETIME NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_2E443EF2A76ED395 (user_id), INDEX IDX_2E443EF2F909284E (concept_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE annotation ADD CONSTRAINT FK_2E443EF2A76ED395 FOREIGN KEY (user_id) REFERENCES user__table (id)');
    $this->addSql('ALTER TABLE annotation ADD CONSTRAINT FK_2E443EF2F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE annotation');
  }
}

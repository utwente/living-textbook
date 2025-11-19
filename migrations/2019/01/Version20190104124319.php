<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190104124319 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE learning_path_element (id INT AUTO_INCREMENT NOT NULL, learning_path_id INT NOT NULL, concept_id INT NOT NULL, next_id INT DEFAULT NULL, description VARCHAR(1024) DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_22057D471DCBEE98 (learning_path_id), INDEX IDX_22057D47F909284E (concept_id), INDEX IDX_22057D47AA23F6C8 (next_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE learning_path_element ADD CONSTRAINT FK_22057D471DCBEE98 FOREIGN KEY (learning_path_id) REFERENCES learning_path (id)');
    $this->addSql('ALTER TABLE learning_path_element ADD CONSTRAINT FK_22057D47F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE learning_path_element ADD CONSTRAINT FK_22057D47AA23F6C8 FOREIGN KEY (next_id) REFERENCES learning_path_element (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE learning_path_element DROP FOREIGN KEY FK_22057D47AA23F6C8');
    $this->addSql('DROP TABLE learning_path_element');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

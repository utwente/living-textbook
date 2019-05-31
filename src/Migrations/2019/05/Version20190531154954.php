<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190531154954 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Added annotation comments';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE annotation_comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, annotation_id INT NOT NULL, text LONGTEXT NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_4F16A736A76ED395 (user_id), INDEX IDX_4F16A736E075FC54 (annotation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE annotation_comment ADD CONSTRAINT FK_4F16A736A76ED395 FOREIGN KEY (user_id) REFERENCES user__table (id)');
    $this->addSql('ALTER TABLE annotation_comment ADD CONSTRAINT FK_4F16A736E075FC54 FOREIGN KEY (annotation_id) REFERENCES annotation (id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE annotation_comment');
  }
}

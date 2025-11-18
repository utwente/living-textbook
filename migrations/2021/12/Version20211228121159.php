<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211228121159 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add API token table';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE user_api_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token_id VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, valid_until DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_used DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_7B42780F41DEE7B9 (token_id), INDEX IDX_7B42780FA76ED395 (user_id), INDEX IDX_7B42780F41DEE7B9 (token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    $this->addSql('ALTER TABLE user_api_token ADD CONSTRAINT FK_7B42780FA76ED395 FOREIGN KEY (user_id) REFERENCES user__table (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE user_api_token');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

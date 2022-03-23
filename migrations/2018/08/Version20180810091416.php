<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180810091416 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE user_group_email (user_group_id INT NOT NULL, email VARCHAR(180) NOT NULL, INDEX user_group_idx (user_group_id), INDEX email_idx (email), PRIMARY KEY(user_group_id, email)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE user_group_email ADD CONSTRAINT FK_1EA59E861ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id)');
    $this->addSql('ALTER TABLE user__table CHANGE username username VARCHAR(180) NOT NULL');
    $this->addSql('UPDATE user__table SET username = LOWER(username)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE user_group_email');
    $this->addSql('ALTER TABLE user__table CHANGE username username VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

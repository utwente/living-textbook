<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180518113728 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE user_group (id INT AUTO_INCREMENT NOT NULL, study_area_id INT NOT NULL, group_type VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_8F02BF9D881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE user_group_users (user_group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_EDB4471B1ED93D47 (user_group_id), INDEX IDX_EDB4471BA76ED395 (user_id), PRIMARY KEY(user_group_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9D881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE user_group_users ADD CONSTRAINT FK_EDB4471B1ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id)');
    $this->addSql('ALTER TABLE user_group_users ADD CONSTRAINT FK_EDB4471BA76ED395 FOREIGN KEY (user_id) REFERENCES user__table (id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE user_group_users DROP FOREIGN KEY FK_EDB4471B1ED93D47');
    $this->addSql('DROP TABLE user_group');
    $this->addSql('DROP TABLE user_group_users');
  }
}

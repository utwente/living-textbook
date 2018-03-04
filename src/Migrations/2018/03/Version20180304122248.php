<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180304122248 extends AbstractMigration
{
  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE study_area ADD owner_user_id INT DEFAULT NULL AFTER name');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD5512B18554A FOREIGN KEY (owner_user_id) REFERENCES user__table (id)');
    $this->addSql('CREATE INDEX IDX_A84CD5512B18554A ON study_area (owner_user_id)');
  }

  public function down(Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD5512B18554A');
    $this->addSql('DROP INDEX IDX_A84CD5512B18554A ON study_area');
    $this->addSql('ALTER TABLE study_area DROP owner_user_id');
  }
}

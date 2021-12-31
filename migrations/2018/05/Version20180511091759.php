<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180511091759 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD5512B18554A');
    $this->addSql('ALTER TABLE study_area CHANGE owner_user_id owner_user_id INT NOT NULL');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD5512B18554A FOREIGN KEY (owner_user_id) REFERENCES user__table (id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD5512B18554A');
    $this->addSql('ALTER TABLE study_area CHANGE owner_user_id owner_user_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD5512B18554A FOREIGN KEY (owner_user_id) REFERENCES user__table (id)');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

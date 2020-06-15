<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615081907 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add default tag filter to study area';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE study_area ADD default_tag_filter_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD551F483146A FOREIGN KEY (default_tag_filter_id) REFERENCES tag (id)');
    $this->addSql('CREATE INDEX IDX_A84CD551F483146A ON study_area (default_tag_filter_id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD551F483146A');
    $this->addSql('DROP INDEX IDX_A84CD551F483146A ON study_area');
    $this->addSql('ALTER TABLE study_area DROP default_tag_filter_id');
  }
}

<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180406091332 extends AbstractMigration
{
  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE concept_study_area');
    $this->addSql('ALTER TABLE concept ADD study_area_id INT DEFAULT NULL');

    // Update values to be able to set the column to non-null
    $this->addSql('UPDATE concept SET study_area_id = 1');
    $this->addSql('ALTER TABLE concept CHANGE study_area_id study_area_id INT NOT NULL');

    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A6050881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('CREATE INDEX IDX_E74A6050881ABDFE ON concept (study_area_id)');
  }

  public function down(Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE concept_study_area (id INT AUTO_INCREMENT NOT NULL, concept_id INT DEFAULT NULL, study_area_id INT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_A0F7599CF909284E (concept_id), INDEX IDX_A0F7599C881ABDFE (study_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE concept_study_area ADD CONSTRAINT FK_A0F7599C881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE concept_study_area ADD CONSTRAINT FK_A0F7599CF909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A6050881ABDFE');
    $this->addSql('DROP INDEX IDX_E74A6050881ABDFE ON concept');
    $this->addSql('ALTER TABLE concept DROP study_area_id');
  }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704142741 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Moved Dotron layout configurations';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('CREATE TABLE layout_configuration (
          id INT AUTO_INCREMENT NOT NULL,
          study_area_id INT NOT NULL,
          layouts LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\',
          created_at DATETIME NOT NULL,
          created_by VARCHAR(255) DEFAULT NULL,
          updated_at DATETIME DEFAULT NULL,
          updated_by VARCHAR(255) DEFAULT NULL,
          deleted_at DATETIME DEFAULT NULL,
          deleted_by VARCHAR(255) DEFAULT NULL,
          INDEX IDX_3993E99F881ABDFE (study_area_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    $this->addSql('ALTER TABLE layout_configuration ADD CONSTRAINT FK_3993E99F881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('ALTER TABLE study_area ADD default_styling_configuration_id INT DEFAULT NULL, ADD default_layout_configuration_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD5514C0A0AFD FOREIGN KEY (default_styling_configuration_id) REFERENCES styling_configuration (id)');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD55168E3C9FE FOREIGN KEY (default_layout_configuration_id) REFERENCES layout_configuration (id)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_A84CD5514C0A0AFD ON study_area (default_styling_configuration_id)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_A84CD55168E3C9FE ON study_area (default_layout_configuration_id)');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD55168E3C9FE');
    $this->addSql('DROP TABLE layout_configuration');
    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD5514C0A0AFD');
    $this->addSql('DROP INDEX UNIQ_A84CD5514C0A0AFD ON study_area');
    $this->addSql('DROP INDEX UNIQ_A84CD55168E3C9FE ON study_area');
    $this->addSql('ALTER TABLE study_area DROP default_styling_configuration_id, DROP default_layout_configuration_id');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

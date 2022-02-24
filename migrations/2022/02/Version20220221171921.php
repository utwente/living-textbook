<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220221171921 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add dotron related entity fields';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE concept ADD dotron_config LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    $this->addSql('ALTER TABLE study_area ADD dotron_config LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    $this->addSql('ALTER TABLE study_area_group ADD dotron TINYINT(1) NOT NULL AFTER name');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE concept DROP dotron_config');
    $this->addSql('ALTER TABLE study_area DROP dotron_config');
    $this->addSql('ALTER TABLE study_area_group DROP dotron');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220228134802 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Adding dotron configuration to concept relation';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE concept_relation ADD dotron_config LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE concept_relation DROP dotron_config');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

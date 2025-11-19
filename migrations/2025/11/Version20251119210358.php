<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251119210358 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Drop comments from existing JSON columns';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE layout_configuration CHANGE layouts layouts JSON DEFAULT NULL');
    $this->addSql('ALTER TABLE layout_configuration_override CHANGE override override JSON DEFAULT NULL');
    $this->addSql('ALTER TABLE styling_configuration_concept_override CHANGE override override JSON DEFAULT NULL');
    $this->addSql('ALTER TABLE styling_configuration_relation_override CHANGE override override JSON DEFAULT NULL');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE layout_configuration CHANGE layouts layouts JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE layout_configuration_override CHANGE override override JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE styling_configuration_concept_override CHANGE override override JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE styling_configuration_relation_override CHANGE override override JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

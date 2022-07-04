<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704143312 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Move dotronconfig from study area to layout configuration';
  }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO layout_configuration (study_area_id, layouts, created_at) SELECT id, dotron_config, NOW() FROM study_area');
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study_area DROP dotron_config');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->throwIrreversibleMigrationException();
    }

    public function isTransactional(): bool
    {
        return true;
    }
}

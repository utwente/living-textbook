<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220926104158 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Remove layout configuration override unique constraint';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('DROP INDEX concept_layout_join ON layout_configuration_override');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('CREATE UNIQUE INDEX concept_layout_join ON layout_configuration_override (concept_id, layout_configuration_id, deleted_at)');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

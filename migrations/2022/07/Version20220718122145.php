<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220718122145 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Add names to layout and styling configurations';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE layout_configuration ADD name VARCHAR(255) NOT NULL AFTER study_area_id');
    $this->addSql('ALTER TABLE styling_configuration ADD name VARCHAR(255) NOT NULL AFTER study_area_id');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE layout_configuration DROP name');
    $this->addSql('ALTER TABLE styling_configuration DROP name');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

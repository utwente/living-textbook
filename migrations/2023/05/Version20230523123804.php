<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230523123804 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'adding description attribute to tag';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE tag ADD description LONGTEXT DEFAULT NULL AFTER color');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE tag DROP description');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

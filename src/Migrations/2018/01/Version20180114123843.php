<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180114123843 extends AbstractMigration
{
  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE node_relation RENAME concept_relation');
    $this->addSql('ALTER TABLE node RENAME concept');
    $this->addSql('SET foreign_key_checks = 0');
    $this->addSql('ALTER TABLE concept_relation DROP INDEX idx_dc245f1c953c1c61, ADD INDEX IDX_5CAE2E9953C1C61 (source_id)');
    $this->addSql('ALTER TABLE concept_relation DROP INDEX idx_dc245f1c158e0b66, ADD INDEX IDX_5CAE2E9158E0B66 (target_id)');
    $this->addSql('ALTER TABLE concept_relation DROP INDEX idx_dc245f1c3bf454a4, ADD INDEX IDX_5CAE2E93BF454A4 (relation_type)');
    $this->addSql('SET foreign_key_checks = 1');
  }

  public function down(Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE concept_relation RENAME node_relation');
    $this->addSql('ALTER TABLE concept RENAME node');
    $this->addSql('SET foreign_key_checks = 0');
    $this->addSql('ALTER TABLE node_relation DROP INDEX idx_5cae2e93bf454a4, ADD INDEX IDX_DC245F1C3BF454A4 (source_id)');
    $this->addSql('ALTER TABLE node_relation DROP INDEX idx_5cae2e9953c1c61, ADD INDEX IDX_DC245F1C953C1C61 (target_id)');
    $this->addSql('ALTER TABLE node_relation DROP INDEX idx_5cae2e9158e0b66, ADD INDEX IDX_DC245F1C158E0B66 (relation_type)');
    $this->addSql('SET foreign_key_checks = 1');

  }
}

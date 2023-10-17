<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704130200 extends AbstractMigration
{
  private const COLUMNS = [
    'concept'               => 'dotron_config',
    'concept_relation'      => 'dotron_config',
    'study_area'            => 'dotron_config',
    'styling_configuration' => 'stylings',
  ];

  public function getDescription(): string
  {
    return 'Change dotron_config storage to JSON';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE concept CHANGE dotron_config dotron_config LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE concept_relation CHANGE dotron_config dotron_config LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE study_area CHANGE dotron_config dotron_config LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE styling_configuration CHANGE stylings stylings LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');

    foreach (self::COLUMNS as $table => $column) {
      /** @noinspection SqlNoDataSourceInspection */
      $query = sprintf('SELECT id, %2$s FROM %1$s WHERE %2$s IS NOT NULL', $table, $column);
      $rows  = $this->connection->iterateAssociative($query);
      foreach ($rows as $row) {
        $dotronConfig = unserialize($row[$column]);

        $query = sprintf('UPDATE %1$s SET %2$s = ? WHERE id = ?', $table, $column);
        $this->addSql($query, [$dotronConfig === null ? null : json_encode($dotronConfig), $row['id']]);
      }
    }
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE concept CHANGE dotron_config dotron_config LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    $this->addSql('ALTER TABLE concept_relation CHANGE dotron_config dotron_config LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    $this->addSql('ALTER TABLE study_area CHANGE dotron_config dotron_config LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    $this->addSql('ALTER TABLE styling_configuration CHANGE stylings stylings LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');

    foreach (self::COLUMNS as $table => $column) {
      /** @noinspection SqlNoDataSourceInspection */
      $query = sprintf('SELECT id, %2$s FROM %1$s WHERE %2$s IS NOT NULL', $table, $column);
      $rows  = $this->connection->iterateAssociative($query);
      foreach ($rows as $row) {
        $dotronConfig = json_decode($row[$column], true);

        $query = sprintf('UPDATE %1$s SET %2$s = ? WHERE id = ?', $table, $column);
        $this->addSql($query, [serialize($dotronConfig), $row['id']]);
      }
    }
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

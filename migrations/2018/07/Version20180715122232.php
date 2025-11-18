<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180715122232 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    // Create new table
    $this->addSql('CREATE TABLE concepts_external_resources (concept_id INT NOT NULL, external_resource_id INT NOT NULL, INDEX IDX_210C8329F909284E (concept_id), INDEX IDX_210C8329E1F5D052 (external_resource_id), PRIMARY KEY(concept_id, external_resource_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

    // Migrate data
    $this->addSql('INSERT INTO concepts_external_resources (concept_id, external_resource_id) SELECT c.id, e.id FROM external_resource e INNER JOIN data_external_resources d ON e.collection_id = d.id INNER JOIN concept c ON c.external_resources_id = d.id');

    // Update rest of database
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A605062F5782D');
    $this->addSql('ALTER TABLE external_resource DROP FOREIGN KEY FK_539D46E5514956FD');
    $this->addSql('ALTER TABLE concepts_external_resources ADD CONSTRAINT FK_210C8329F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE concepts_external_resources ADD CONSTRAINT FK_210C8329E1F5D052 FOREIGN KEY (external_resource_id) REFERENCES external_resource (id)');
    $this->addSql('DROP TABLE data_external_resources');
    $this->addSql('DROP INDEX IDX_539D46E5514956FD ON external_resource');
    $this->addSql('ALTER TABLE external_resource DROP collection_id, DROP position');
    $this->addSql('DROP INDEX UNIQ_E74A605062F5782D ON concept');
    $this->addSql('ALTER TABLE concept DROP external_resources_id');
  }

  public function down(Schema $schema): void
  {
    // Create new table/columns
    $this->addSql('CREATE TABLE data_external_resources (id INT AUTO_INCREMENT NOT NULL, concept_id INT NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE concept ADD external_resources_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE external_resource ADD collection_id INT DEFAULT NULL, ADD position INT DEFAULT NULL');

    // Migrate data
    $this->addSql('INSERT INTO data_external_resources (concept_id, created_at, created_by) SELECT c.id, NOW(), "Migration" FROM concept c');
    $this->addSql('UPDATE concept c INNER JOIN data_external_resources der ON c.id = der.concept_id SET c.external_resources_id = der.id');
    $this->addSql('UPDATE external_resource er
INNER JOIN concepts_external_resources cer ON cer.external_resource_id = er.id
INNER JOIN concept c ON c.id = cer.concept_id
INNER JOIN data_external_resources der ON der.id = c.external_resources_id
SET er.collection_id = der.id');
    $this->addSql('UPDATE external_resource SET position = 0');

    // Fix tables
    $this->addSql('ALTER TABLE data_external_resources DROP concept_id');
    $this->addSql('ALTER TABLE concept CHANGE external_resources_id external_resources_id INT NOT NULL');
    $this->addSql('ALTER TABLE external_resource CHANGE collection_id collection_id INT NOT NULL');
    $this->addSql('ALTER TABLE external_resource CHANGE position position INT NOT NULL');

    // Update rest of database
    $this->addSql('DROP TABLE concepts_external_resources');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A605062F5782D FOREIGN KEY (external_resources_id) REFERENCES data_external_resources (id)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_E74A605062F5782D ON concept (external_resources_id)');
    $this->addSql('ALTER TABLE external_resource ADD CONSTRAINT FK_539D46E5514956FD FOREIGN KEY (collection_id) REFERENCES data_external_resources (id)');
    $this->addSql('CREATE INDEX IDX_539D46E5514956FD ON external_resource (collection_id)');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180715124248 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE external_resource ADD study_area_id INT DEFAULT NULL AFTER id');

    $this->addSql('UPDATE external_resource er
  INNER JOIN concepts_external_resources cer on er.id = cer.external_resource_id
  INNER JOIN concept c on cer.concept_id = c.id
SET er.study_area_id = c.study_area_id
');

    $this->addSql('ALTER TABLE external_resource CHANGE study_area_id study_area_id INT NOT NULL');
    $this->addSql('ALTER TABLE external_resource ADD CONSTRAINT FK_539D46E5881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('CREATE INDEX IDX_539D46E5881ABDFE ON external_resource (study_area_id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE external_resource DROP FOREIGN KEY FK_539D46E5881ABDFE');
    $this->addSql('DROP INDEX IDX_539D46E5881ABDFE ON external_resource');
    $this->addSql('ALTER TABLE external_resource DROP study_area_id');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}

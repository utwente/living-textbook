<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261106114942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add deprecated skill object name configuration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE study_area_field_configuration ADD skill_obj_name VARCHAR(50) DEFAULT NULL AFTER learning_outcome_obj_name');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE study_area_field_configuration DROP skill_obj_name');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}

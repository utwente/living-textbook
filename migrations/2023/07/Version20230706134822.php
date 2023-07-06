<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230706134822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'export studyarea to a given url';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study_area ADD url_export_enabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE study_area ADD export_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study_area DROP url_export_enabled');
        $this->addSql('ALTER TABLE study_area DROP export_url');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130190038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding definition as reference table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE data_definition (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE concept ADD definition_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A6050D11EA911 FOREIGN KEY (definition_id) REFERENCES data_definition (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E74A6050D11EA911 ON concept (definition_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A6050D11EA911');
        $this->addSql('DROP TABLE data_definition');
        $this->addSql('DROP INDEX UNIQ_E74A6050D11EA911 ON concept');
        $this->addSql('ALTER TABLE concept ADD definition LONGTEXT DEFAULT NULL, DROP definition_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}

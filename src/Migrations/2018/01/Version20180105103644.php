<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180105103644 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE node (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE relation_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE node_relation (id INT AUTO_INCREMENT NOT NULL, left_node_id INT NOT NULL, right_node_id INT NOT NULL, relation_type INT NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_DC245F1C51DF9AB1 (left_node_id), INDEX IDX_DC245F1C21D02DF1 (right_node_id), INDEX IDX_DC245F1C3BF454A4 (relation_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE node_relation ADD CONSTRAINT FK_DC245F1C51DF9AB1 FOREIGN KEY (left_node_id) REFERENCES node (id)');
        $this->addSql('ALTER TABLE node_relation ADD CONSTRAINT FK_DC245F1C21D02DF1 FOREIGN KEY (right_node_id) REFERENCES node (id)');
        $this->addSql('ALTER TABLE node_relation ADD CONSTRAINT FK_DC245F1C3BF454A4 FOREIGN KEY (relation_type) REFERENCES relation_type (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE node_relation DROP FOREIGN KEY FK_DC245F1C51DF9AB1');
        $this->addSql('ALTER TABLE node_relation DROP FOREIGN KEY FK_DC245F1C21D02DF1');
        $this->addSql('ALTER TABLE node_relation DROP FOREIGN KEY FK_DC245F1C3BF454A4');
        $this->addSql('DROP TABLE node');
        $this->addSql('DROP TABLE relation_type');
        $this->addSql('DROP TABLE node_relation');
    }
}

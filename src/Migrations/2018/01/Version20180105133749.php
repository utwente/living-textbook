<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180105133749 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE node_relation DROP FOREIGN KEY FK_DC245F1C21D02DF1');
        $this->addSql('ALTER TABLE node_relation DROP FOREIGN KEY FK_DC245F1C51DF9AB1');
        $this->addSql('DROP INDEX IDX_DC245F1C51DF9AB1 ON node_relation');
        $this->addSql('DROP INDEX IDX_DC245F1C21D02DF1 ON node_relation');
        $this->addSql('ALTER TABLE node_relation CHANGE left_node_id source_id INT NOT NULL, CHANGE right_node_id target_id INT NOT NULL');
        $this->addSql('ALTER TABLE node_relation ADD CONSTRAINT FK_DC245F1C953C1C61 FOREIGN KEY (source_id) REFERENCES node (id)');
        $this->addSql('ALTER TABLE node_relation ADD CONSTRAINT FK_DC245F1C158E0B66 FOREIGN KEY (target_id) REFERENCES node (id)');
        $this->addSql('CREATE INDEX IDX_DC245F1C953C1C61 ON node_relation (source_id)');
        $this->addSql('CREATE INDEX IDX_DC245F1C158E0B66 ON node_relation (target_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE node_relation DROP FOREIGN KEY FK_DC245F1C953C1C61');
        $this->addSql('ALTER TABLE node_relation DROP FOREIGN KEY FK_DC245F1C158E0B66');
        $this->addSql('DROP INDEX IDX_DC245F1C953C1C61 ON node_relation');
        $this->addSql('DROP INDEX IDX_DC245F1C158E0B66 ON node_relation');
        $this->addSql('ALTER TABLE node_relation CHANGE source_id left_node_id INT NOT NULL, CHANGE target_id right_node_id INT NOT NULL');
        $this->addSql('ALTER TABLE node_relation ADD CONSTRAINT FK_DC245F1C21D02DF1 FOREIGN KEY (right_node_id) REFERENCES node (id)');
        $this->addSql('ALTER TABLE node_relation ADD CONSTRAINT FK_DC245F1C51DF9AB1 FOREIGN KEY (left_node_id) REFERENCES node (id)');
        $this->addSql('CREATE INDEX IDX_DC245F1C51DF9AB1 ON node_relation (left_node_id)');
        $this->addSql('CREATE INDEX IDX_DC245F1C21D02DF1 ON node_relation (right_node_id)');
    }
}

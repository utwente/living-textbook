<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180413102418 extends AbstractMigration
{
  /**
   * @param Schema $schema
   *
   * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
   */
  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE concepts_prior_knowledge (concept_id INT NOT NULL, prior_knowledge_id INT NOT NULL, INDEX IDX_4411F4B5F909284E (concept_id), INDEX IDX_4411F4B555853A57 (prior_knowledge_id), PRIMARY KEY(concept_id, prior_knowledge_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE concepts_prior_knowledge ADD CONSTRAINT FK_4411F4B5F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
    $this->addSql('ALTER TABLE concepts_prior_knowledge ADD CONSTRAINT FK_4411F4B555853A57 FOREIGN KEY (prior_knowledge_id) REFERENCES concept (id)');
  }

  /**
   * @param Schema $schema
   *
   * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
   */
  public function down(Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE concepts_prior_knowledge');
  }
}

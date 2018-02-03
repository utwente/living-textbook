<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Database\Migration\ContainerAwareMigration;
use App\Entity\Concept;
use App\Entity\Data\DataExamples;
use App\Entity\Data\DataHowTo;
use App\Entity\Data\DataSelfAssessment;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180203195334 extends AbstractMigration implements ContainerAwareInterface
{

  use ContainerAwareMigration;

  /**
   * @param Schema $schema
   *
   * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
   */
  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE data_examples (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE data_how_to (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE data_self_assessment (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE concept ADD how_to_id INT DEFAULT NULL, ADD examples_id INT DEFAULT NULL, ADD self_assessment_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A60502A796CFA FOREIGN KEY (how_to_id) REFERENCES data_how_to (id)');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A60508D6A9A10 FOREIGN KEY (examples_id) REFERENCES data_examples (id)');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A60509ECF1EA3 FOREIGN KEY (self_assessment_id) REFERENCES data_self_assessment (id)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_E74A60502A796CFA ON concept (how_to_id)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_E74A60508D6A9A10 ON concept (examples_id)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_E74A60509ECF1EA3 ON concept (self_assessment_id)');
  }

  /**
   * @param Schema $schema
   *
   * @throws \Doctrine\ORM\ORMException
   * @throws \Doctrine\ORM\OptimisticLockException
   * @throws \ReflectionException
   * @throws \Doctrine\DBAL\DBALException
   */
  public function postUp(Schema $schema)
  {
    // Generate introduction objects for the existing concepts
    $em       = $this->container->get('doctrine.orm.entity_manager');
    $concepts = $em->getRepository('App:Concept')->findAll();

    // Setup reflection property
    $reflClass = new \ReflectionClass(Concept::class);
    $reflProp1 = $reflClass->getProperty('howTo');
    $reflProp1->setAccessible(true);
    $reflProp2 = $reflClass->getProperty('examples');
    $reflProp2->setAccessible(true);
    $reflProp3 = $reflClass->getProperty('selfAssessment');
    $reflProp3->setAccessible(true);

    // Loop concepts
    foreach ($concepts as $concept) {
      /** @var Concept $concept */

      if ($reflProp1->getValue($concept) === NULL) {
        $concept->setHowTo(new DataHowTo());
      }
      if ($reflProp2->getValue($concept) === NULL) {
        $concept->setExamples(new DataExamples());
      }
      if ($reflProp3->getValue($concept) === NULL) {
        $concept->setSelfAssessment(new DataSelfAssessment());
      }
    }

    // Save data
    $em->flush();

    // Update database
    $this->connection->executeQuery('set foreign_key_checks = off');
    $this->connection->executeQuery('ALTER TABLE concept CHANGE how_to_id how_to_id INT NOT NULL');
    $this->connection->executeQuery('ALTER TABLE concept CHANGE examples_id examples_id INT NOT NULL');
    $this->connection->executeQuery('ALTER TABLE concept CHANGE self_assessment_id self_assessment_id INT NOT NULL');
    $this->connection->executeQuery('set foreign_key_checks = on');
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

    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A60508D6A9A10');
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A60502A796CFA');
    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A60509ECF1EA3');
    $this->addSql('DROP TABLE data_examples');
    $this->addSql('DROP TABLE data_how_to');
    $this->addSql('DROP TABLE data_self_assessment');
    $this->addSql('DROP INDEX UNIQ_E74A60502A796CFA ON concept');
    $this->addSql('DROP INDEX UNIQ_E74A60508D6A9A10 ON concept');
    $this->addSql('DROP INDEX UNIQ_E74A60509ECF1EA3 ON concept');
    $this->addSql('ALTER TABLE concept DROP how_to_id, DROP examples_id, DROP self_assessment_id');
  }
}

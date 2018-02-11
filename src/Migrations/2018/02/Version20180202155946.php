<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Database\Migration\ContainerAwareMigration;
use App\Entity\Concept;
use App\Entity\Data\DataTheoryExplanation;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180202155946 extends AbstractMigration implements ContainerAwareInterface
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

    $this->addSql('CREATE TABLE data_theory_explanation (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE concept ADD theory_explanation_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A6050AB56AAE7 FOREIGN KEY (theory_explanation_id) REFERENCES data_theory_explanation (id)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_E74A6050AB56AAE7 ON concept (theory_explanation_id)');

    $this->addSql('ALTER TABLE data_learning_outcomes CHANGE learning_outcomes text LONGTEXT DEFAULT NULL');
    $this->addSql('ALTER TABLE data_introduction CHANGE introduction text LONGTEXT DEFAULT NULL');
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
    $concepts = $em->getRepository('App:Concept')->createQueryBuilder('c')
        ->select('c.id')
        ->join('c.theoryExplanation', 'te')
        ->getQuery()->getResult();

    // Setup reflection property
    $reflClass = new \ReflectionClass(Concept::class);
    $reflProp  = $reflClass->getProperty('theoryExplanation');
    $reflProp->setAccessible(true);

    // Loop concepts
    foreach ($concepts as $concept) {
      /** @var Concept $concept */

      if ($reflProp->getValue($concept) === NULL) {
        $concept->setTheoryExplanation(new DataTheoryExplanation());
      }
    }

    // Save data
    $em->flush();

    // Update database
    $this->connection->executeQuery('set foreign_key_checks = off');
    $this->connection->executeQuery('ALTER TABLE concept CHANGE theory_explanation_id theory_explanation_id INT NOT NULL');
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

    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A6050AB56AAE7');
    $this->addSql('DROP TABLE data_theory_explanation');
    $this->addSql('DROP INDEX UNIQ_E74A6050AB56AAE7 ON concept');
    $this->addSql('ALTER TABLE concept DROP theory_explanation_id');
    $this->addSql('ALTER TABLE data_introduction ADD introduction LONGTEXT NOT NULL COLLATE utf8_unicode_ci, DROP text');
    $this->addSql('ALTER TABLE data_learning_outcomes CHANGE text learning_outcomes LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
  }
}

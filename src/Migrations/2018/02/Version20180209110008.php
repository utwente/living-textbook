<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Database\Migration\ContainerAwareMigration;
use App\Entity\Concept;
use App\Entity\Data\DataExternalResources;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180209110008 extends AbstractMigration implements ContainerAwareInterface
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

    $this->addSql('CREATE TABLE external_resource (id INT AUTO_INCREMENT NOT NULL, collection_id INT NOT NULL, description TINYTEXT NOT NULL, url TEXT NOT NULL, broken TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, INDEX IDX_539D46E5514956FD (collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE data_external_resources (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE external_resource ADD CONSTRAINT FK_539D46E5514956FD FOREIGN KEY (collection_id) REFERENCES data_external_resources (id)');
    $this->addSql('ALTER TABLE concept ADD external_resources_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A605062F5782D FOREIGN KEY (external_resources_id) REFERENCES data_external_resources (id)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_E74A605062F5782D ON concept (external_resources_id)');
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
        ->join('c.externalResources', 'er')
        ->getQuery()->getResult();

    // Setup reflection property
    $reflClass = new \ReflectionClass(Concept::class);
    $reflProp  = $reflClass->getProperty('externalResources');
    $reflProp->setAccessible(true);

    // Loop concepts
    foreach ($concepts as $concept) {
      /** @var Concept $concept */

      if ($reflProp->getValue($concept) === NULL) {
        $concept->setExternalResources(new DataExternalResources());
      }
    }

    // Save data
    $em->flush();

    // Update database
    $this->connection->executeQuery('set foreign_key_checks = off');
    $this->connection->executeQuery('ALTER TABLE concept CHANGE external_resources_id external_resources_id INT NOT NULL');
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

    $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A605062F5782D');
    $this->addSql('ALTER TABLE external_resource DROP FOREIGN KEY FK_539D46E5514956FD');
    $this->addSql('DROP TABLE external_resource');
    $this->addSql('DROP TABLE data_external_resources');
    $this->addSql('DROP INDEX UNIQ_E74A605062F5782D ON concept');
    $this->addSql('ALTER TABLE concept DROP external_resources_id');
  }
}

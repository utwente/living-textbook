<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Database\Migration\ContainerAwareMigration;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180508182519 extends AbstractMigration implements ContainerAwareInterface
{
  use ContainerAwareMigration;

  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation_type ADD study_area_id INT NOT NULL');

    // Set all current relation types as used by the first available study area
    $this->addSql('UPDATE relation_type SET study_area_id = (SELECT id FROM study_area ORDER BY id LIMIT 1)');

    $this->addSql('ALTER TABLE relation_type ADD CONSTRAINT FK_3BF454A4881ABDFE FOREIGN KEY (study_area_id) REFERENCES study_area (id)');
    $this->addSql('CREATE INDEX IDX_3BF454A4881ABDFE ON relation_type (study_area_id)');
  }

  /**
   * @param Schema $schema
   *
   * @throws \Doctrine\ORM\ORMException
   * @throws \Doctrine\ORM\OptimisticLockException
   */
  public function postUp(Schema $schema)
  {
    $em = $this->container->get('doctrine.orm.entity_manager');

    // Retrieve all relations
    $relations                    = $em->getRepository('App:ConceptRelation')->findAll();
    $relationTypeStudyAreaMapping = [];
    foreach ($relations as $relation) {
      $relationTypeId = $relation->getRelationType()->getId();
      if (!array_key_exists($relationTypeId, $relationTypeStudyAreaMapping)) {
        $relationTypeStudyAreaMapping[$relationTypeId] = [];
      }
      $studyAreaId = $relation->getSource()->getStudyArea()->getId();
      if ($relation->getRelationType()->getStudyArea()->getId() != $studyAreaId &&
          !in_array($studyAreaId, $relationTypeStudyAreaMapping[$relationTypeId])) {
        $relationTypeStudyAreaMapping[$relationTypeId][] = $studyAreaId;
      }
    }

    // Create copies in used areas
    $results = [];
    foreach ($relationTypeStudyAreaMapping as $relationTypeId => $studyAreaIds) {
      $relationType = $em->getRepository('App:RelationType')->find($relationTypeId);
      $results[$relationTypeId] = [];
      foreach ($studyAreaIds as $studyAreaId) {
        // Copy the original one for the new area
        $new = clone($relationType);
        $new->setStudyArea($em->getRepository('App:StudyArea')->find($studyAreaId));
        $em->persist($new);
        $results[$relationTypeId][$studyAreaId] = $new;
      }
    }

    // Loop the relations again, in order to assign the correct study area ones
    foreach ($relations as $relation) {
      $studyAreaId = $relation->getSource()->getStudyArea()->getId();
      if ($relation->getRelationType()->getStudyArea()->getId() != $studyAreaId){
        $relation->setRelationType($results[$relation->getRelationType()->getId()][$studyAreaId]);
      }
    }

    $em->flush();
  }

  public function down(Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation_type DROP FOREIGN KEY FK_3BF454A4881ABDFE');
    $this->addSql('DROP INDEX IDX_3BF454A4881ABDFE ON relation_type');
    $this->addSql('ALTER TABLE relation_type DROP study_area_id');
  }
}

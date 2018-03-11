<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Database\Migration\ContainerAwareMigration;
use App\Entity\Concept;
use App\Entity\ConceptStudyArea;
use App\Entity\StudyArea;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180202132958 extends AbstractMigration implements ContainerAwareInterface
{

  use ContainerAwareMigration;

  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
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

    // Generate a study area, if not yet available
    $studyArea = $em->getRepository('App:StudyArea')->findBy([], NULL, 1);
    if (!$studyArea) {
      $studyArea = new StudyArea();
      $studyArea->setName('Default');
      $em->persist($studyArea);
    } else {
      $studyArea = $studyArea[0];
    }

    // Generate default study-area-concept relations
    $concepts = $em->getRepository('App:Concept')->createQueryBuilder('c')
        ->select('c.id')
        ->join('c.studyAreas', 'sa')
        ->getQuery()->getResult();
    foreach ($concepts as $concept) {
      assert($concept instanceof Concept);
      if (count($concept->getStudyAreas()) == 0) {
        $concept->addStudyArea((new ConceptStudyArea())->setStudyArea($studyArea));
      }
    }

    // Save data
    $em->flush();
  }

  public function down(Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs

  }
}

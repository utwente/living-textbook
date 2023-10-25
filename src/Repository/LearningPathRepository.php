<?php

namespace App\Repository;

use App\Entity\Concept;
use App\Entity\LearningPath;
use App\Entity\LearningPathElement;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class LearningPathRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, LearningPath::class);
  }

  /** @return LearningPath[] */
  public function findForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
      ->orderBy('lp.name', 'ASC')
      ->getQuery()->getResult();
  }

  /** @throws NonUniqueResultException */
  public function getCountForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
      ->select('COUNT(lp.id)')
      ->getQuery()->getSingleScalarResult();
  }

  public function findForStudyAreaQb(StudyArea $studyArea): QueryBuilder
  {
    return $this->createQueryBuilder('lp')
      ->where('lp.studyArea = :studyArea')
      ->setParameter('studyArea', $studyArea);
  }

  /**
   * Find learning path that use the given concept.
   *
   * @return LearningPath[]
   */
  public function findForConcept(Concept $concept)
  {
    return $this->createQueryBuilder('lp')
      ->distinct()
      ->leftJoin('lp.elements', 'lpe')
      ->where('lpe.concept = :concept')
      ->andWhere('lpe.id is not null')
      ->setParameter('concept', $concept)
      ->getQuery()->getResult();
  }

  /**
   * Remove elements based on the concept.
   *
   * @throws ORMException
   */
  public function removeElementBasedOnConcept(Concept $concept)
  {
    $em            = $this->getEntityManager();
    $learningPaths = $this->findForConcept($concept);
    foreach ($learningPaths as $learningPath) {
      /** @var LearningPathElement[] $elements */
      $elements     = array_values($learningPath->getElementsOrdered()->toArray());
      $elementCount = count($elements);
      $removed      = [];
      for ($index = 0; $index < $elementCount; $index++) {
        if ($elements[$index]->getConcept()->getId() === $concept->getId()) {
          // Remove this element
          $em->remove($elements[$index]);

          // Update previous element' description
          if ($index != 0) {
            $elements[$index - 1]->setDescription(null);
          }

          $removed[] = $index;
        }
      }

      // Loop again (but other way around) to set next references correctly, by skipped removed elements
      $previousElement = null;
      for ($index = $elementCount - 1; $index >= 0; $index--) {
        // Skip removed items
        if (in_array($index, $removed)) {
          continue;
        }

        // Update element
        $elements[$index]->setNext($previousElement);
        $previousElement = $elements[$index];
      }
    }
  }
}

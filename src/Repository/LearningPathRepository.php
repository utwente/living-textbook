<?php

namespace App\Repository;

use App\Entity\Concept;
use App\Entity\LearningPath;
use App\Entity\LearningPathElement;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class LearningPathRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, LearningPath::class);
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return LearningPath[]
   */
  public function findForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
        ->orderBy('lp.name', 'ASC')
        ->getQuery()->getResult();
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return mixed
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function getCountForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
        ->select('COUNT(lp.id)')
        ->getQuery()->getSingleScalarResult();
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return QueryBuilder
   */
  public function findForStudyAreaQb(StudyArea $studyArea): QueryBuilder
  {
    return $this->createQueryBuilder('lp')
        ->where('lp.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea);
  }

  /**
   * Find learning path that use the given concept
   *
   * @param Concept $concept
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
   * Remove elements based on the concept
   *
   * @param Concept $concept
   *
   * @throws \Doctrine\ORM\ORMException
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
            $elements[$index - 1]->setDescription(NULL);
          }

          $removed[] = $index;
        }
      }

      // Loop again (but other way around) to set next references correctly, by skipped removed elements
      $previousElement = NULL;
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

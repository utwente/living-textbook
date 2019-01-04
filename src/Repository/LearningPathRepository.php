<?php

namespace App\Repository;

use App\Entity\LearningPath;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
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
        ->getQuery()->getResult();
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
        ->setParameter('studyArea', $studyArea)
        ->orderBy('lp.name', 'ASC');
  }
}

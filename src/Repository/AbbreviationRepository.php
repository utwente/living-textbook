<?php

namespace App\Repository;

use App\Entity\Abbreviation;
use App\Entity\LearningOutcome;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

class AbbreviationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Abbreviation::class);
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return LearningOutcome[]
   */
  public function findForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
        ->getQuery()->getResult();
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function findForStudyAreaQb(StudyArea $studyArea): \Doctrine\ORM\QueryBuilder
  {
    return $this->createQueryBuilder('lo')
        ->where('lo.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->orderBy('lo.abbreviation', 'ASC');
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return mixed
   * @throws NonUniqueResultException
   */
  public function getCountForStudyArea(StudyArea $studyArea)
  {
    return $this->createQueryBuilder('lo')
        ->select('COUNT(lo.id)')
        ->where('lo.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->getQuery()->getSingleScalarResult();
  }
}

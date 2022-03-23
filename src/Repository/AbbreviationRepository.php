<?php

namespace App\Repository;

use App\Entity\Abbreviation;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class AbbreviationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Abbreviation::class);
  }

  /** @return Abbreviation[] */
  public function findForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
        ->getQuery()->getResult();
  }

  public function findForStudyAreaQb(StudyArea $studyArea): \Doctrine\ORM\QueryBuilder
  {
    return $this->createQueryBuilder('a')
        ->where('a.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->orderBy('a.abbreviation', 'ASC');
  }

  /**
   * @throws NonUniqueResultException
   *
   * @return mixed
   */
  public function getCountForStudyArea(StudyArea $studyArea)
  {
    return $this->createQueryBuilder('a')
        ->select('COUNT(a.id)')
        ->where('a.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->getQuery()->getSingleScalarResult();
  }
}

<?php

namespace App\Repository;

use App\Entity\Contributor;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class ContributorRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Contributor::class);
  }

  /** @return Contributor[] */
  public function findForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
      ->getQuery()->getResult();
  }

  public function findForStudyAreaQb(StudyArea $studyArea): QueryBuilder
  {
    return $this->createQueryBuilder('c')
      ->where('c.studyArea = :studyArea')
      ->setParameter('studyArea', $studyArea)
      ->orderBy('c.name', 'ASC');
  }

  /**
   * Find external resources for a set of concepts.
   *
   * @return Contributor[]
   */
  public function findForConcepts(array $concepts)
  {
    return $this->createQueryBuilder('c')
      ->distinct()
      ->leftJoin('c.concepts', 'c')
      ->where('c IN (:concepts)')
      ->setParameter('concepts', $concepts)
      ->getQuery()->getResult();
  }

  /** @throws NonUniqueResultException */
  public function getCountForStudyArea(StudyArea $studyArea)
  {
    return $this->createQueryBuilder('c')
      ->select('COUNT(c.id)')
      ->where('c.studyArea = :studyArea')
      ->setParameter('studyArea', $studyArea)
      ->getQuery()->getSingleScalarResult();
  }
}

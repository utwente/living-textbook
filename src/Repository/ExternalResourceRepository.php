<?php

namespace App\Repository;

use App\Entity\ExternalResource;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class ExternalResourceRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, ExternalResource::class);
  }

  /** @return ExternalResource[] */
  public function findForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
        ->getQuery()->getResult();
  }

  /** @return ExternalResource[] */
  public function findForStudyAreaOrderedByTitle(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
        ->orderBy('er.title', 'ASC')
        ->getQuery()->getResult();
  }

  public function findForStudyAreaQb(StudyArea $studyArea): QueryBuilder
  {
    return $this->createQueryBuilder('er')
        ->where('er.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->orderBy('er.title', 'ASC');
  }

  /**
   * Find external resources for a set of concepts.
   *
   * @return ExternalResource[]
   */
  public function findForConcepts(array $concepts)
  {
    return $this->createQueryBuilder('er')
        ->distinct()
        ->leftJoin('er.concepts', 'c')
        ->where('c IN (:concepts)')
        ->setParameter('concepts', $concepts)
        ->getQuery()->getResult();
  }

  /**
   * @throws NonUniqueResultException
   *
   * @return mixed
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

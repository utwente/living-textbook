<?php

namespace App\Repository;

use App\Entity\ExternalResource;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ExternalResourceRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, ExternalResource::class);
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return ExternalResource[]
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
    return $this->createQueryBuilder('er')
        ->where('er.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->orderBy('er.title', 'ASC');
  }

  /**
   * Find external resources for a set of concepts
   *
   * @param array $concepts
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

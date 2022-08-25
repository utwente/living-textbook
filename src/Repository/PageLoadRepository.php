<?php

namespace App\Repository;

use App\Entity\PageLoad;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PageLoadRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, PageLoad::class);
  }

  /** @return PageLoad[] */
  public function getByStudyAreaOrderedOnIds(StudyArea $studyArea): array
  {
    return $this->createQueryBuilder('pl')
        ->where('pl.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->orderBy('pl.userId')
        ->addOrderBy('pl.sessionId')
        ->addOrderBy('pl.timestamp')
        ->getQuery()->getResult();
  }

  /** Remove all page loads for a certain study area. */
  public function purgeForStudyArea(StudyArea $studyArea)
  {
    $this->createQueryBuilder('pl')
        ->delete()
        ->where('pl.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->getQuery()->execute();
  }
}

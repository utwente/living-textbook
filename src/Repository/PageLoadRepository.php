<?php

namespace App\Repository;

use App\Entity\PageLoad;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;

class PageLoadRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, PageLoad::class);
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return PageLoad[]|Collection
   */
  public function getByStudyAreaOrderedOnIds(StudyArea $studyArea)
  {
    return $this->createQueryBuilder('pl')
        ->where('pl.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->orderBy('pl.userId')
        ->addOrderBy('pl.sessionId')
        ->addOrderBy('pl.timestamp')
        ->getQuery()->getResult();
  }

}

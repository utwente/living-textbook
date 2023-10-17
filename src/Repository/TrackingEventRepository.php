<?php

namespace App\Repository;

use App\Entity\StudyArea;
use App\Entity\TrackingEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TrackingEventRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, TrackingEvent::class);
  }

  /** @return TrackingEvent[] */
  public function getByStudyAreaOrderedOnIds(StudyArea $studyArea): array
  {
    return $this->createQueryBuilder('te')
      ->where('te.studyArea = :studyArea')
      ->setParameter('studyArea', $studyArea)
      ->orderBy('te.userId')
      ->addOrderBy('te.sessionId')
      ->addOrderBy('te.timestamp')
      ->getQuery()->getResult();
  }

  /** Remove all tracking events for a certain study area. */
  public function purgeForStudyArea(StudyArea $studyArea)
  {
    $this->createQueryBuilder('te')
      ->delete()
      ->where('te.studyArea = :studyArea')
      ->setParameter('studyArea', $studyArea)
      ->getQuery()->execute();
  }
}

<?php

namespace App\Repository;

use App\Entity\Review;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Review::class);
  }

  /**
   * Retrieve the submissions for the current area.
   *
   * @return Review[]
   */
  public function getSubmissions(StudyArea $studyArea)
  {
    return $this->createQueryBuilder('r')
      ->where('r.studyArea = :studyArea')
      ->andWhere('r.approvedAt IS NULL')
      ->orderBy('r.requestedReviewAt', 'ASC')
      ->setParameter('studyArea', $studyArea)
      ->getQuery()->getResult();
  }

  /**
   * Retrieve the approved review, order by approval date.
   *
   * @return Review[]
   */
  public function getApproved(StudyArea $studyArea)
  {
    return $this->createQueryBuilder('r')
      ->where('r.studyArea = :studyArea')
      ->andWhere('r.approvedAt IS NOT NULL')
      ->orderBy('r.approvedAt', 'ASC')
      ->setParameter('studyArea', $studyArea)
      ->getQuery()->getResult();
  }
}

<?php

namespace App\Repository;

use App\Entity\PendingChange;
use App\Entity\StudyArea;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class PendingChangeRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, PendingChange::class);
  }

  /**
   * Get all pending changes for the given study area and user
   *
   * @param StudyArea $studyArea
   * @param User      $user
   *
   * @return PendingChange[]
   */
  public function getForUser(StudyArea $studyArea, User $user): array
  {
    return $this->findBy([
        'studyArea' => $studyArea,
        'owner'     => $user,
    ]);
  }

  /**
   * Retrieve the pending changes which can be submitted for review for the given study area and user
   *
   * @param StudyArea $studyArea
   * @param User      $user
   *
   * @return PendingChange[]
   */
  public function getSubmittableForUser(StudyArea $studyArea, User $user): array
  {
    return $this->createQueryBuilder('pc')
        ->where('pc.studyArea = :studyArea')
        ->andWhere('pc.owner = :owner')
        ->andWhere('pc.review IS NULL')
        ->setParameter('studyArea', $studyArea)
        ->setParameter('owner', $user)
        ->orderBy('pc.objectType')
        ->addOrderBy('pc.changeType')
        ->getQuery()->getResult();
  }

  /**
   * Retrieve multiple pending changes at once
   *
   * @param array $ids
   *
   * @return PendingChange[]
   */
  public function getMultiple(array $ids): array
  {
    return $this->createQueryBuilder('pc')
        ->where('pc.id IN (:ids)')
        ->setParameter('ids', $ids)
        ->getQuery()->getResult();
  }
}

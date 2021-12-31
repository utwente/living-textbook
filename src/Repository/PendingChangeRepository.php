<?php

namespace App\Repository;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\PendingChange;
use App\Entity\StudyArea;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

class PendingChangeRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, PendingChange::class);
  }

  /**
   * Get all pending changes for an object
   *
   * @param ReviewableInterface $object
   * @param PendingChange|null  $exclude
   *
   * @return PendingChange[]
   */
  public function getForObject(ReviewableInterface $object, ?PendingChange $exclude = NULL): array
  {
    $qb = $this->createQueryBuilder('pc')
        ->where('pc.objectType = :objectType')
        ->andWhere('pc.objectId = :objectId')
        ->setParameter('objectType', $object->getReviewName())
        ->setParameter('objectId', $object->getId());

    if (NULL !== $exclude) {
      $qb
          ->andWhere('pc.id != :exclude')
          ->setParameter('exclude', $exclude->getId());
    }

    return $qb
        ->getQuery()->getResult();
  }

  /**
   * Retrieve a mergeable pending change for the given one
   *
   * @param PendingChange $pendingChange
   *
   * @return PendingChange|null
   */
  public function getMergeable(PendingChange $pendingChange): ?PendingChange
  {
    try {
      $qb = $this->createQueryBuilder('pc')
          ->where('pc.objectType = :objectType')
          ->andWhere('pc.objectId = :objectId')
          ->andWhere('pc.changeType = :changeType')
          ->andWhere('pc.owner = :owner')
          ->setParameter('objectType', $pendingChange->getObjectType())
          ->setParameter('objectId', $pendingChange->getObjectId())
          ->setParameter('changeType', $pendingChange->getChangeType())
          ->setParameter('owner', $pendingChange->getOwner())
          ->setMaxResults(1);

      if ($pendingChange->getReview()) {
        $qb
            ->andWhere('pc.review = :review')
            ->setParameter('review', $pendingChange->getReview());
      } else {
        $qb->andWhere('pc.review is null');
      }

      return $qb
          ->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException $e) {
      return NULL;
    }
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

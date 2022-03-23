<?php

namespace App\Repository;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\PendingChange;
use App\Entity\StudyArea;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class PendingChangeRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, PendingChange::class);
  }

  /**
   * Get all pending changes for an object.
   *
   * @return PendingChange[]
   */
  public function getForObject(ReviewableInterface $object, ?PendingChange $exclude = null): array
  {
    $qb = $this->createQueryBuilder('pc')
        ->where('pc.objectType = :objectType')
        ->andWhere('pc.objectId = :objectId')
        ->setParameter('objectType', $object->getReviewName())
        ->setParameter('objectId', $object->getId());

    if (null !== $exclude) {
      $qb
          ->andWhere('pc.id != :exclude')
          ->setParameter('exclude', $exclude->getId());
    }

    return $qb
        ->getQuery()->getResult();
  }

  /** Retrieve a mergeable pending change for the given one. */
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
      return null;
    }
  }

  /**
   * Get all pending changes for the given study area and user.
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
   * Retrieve the pending changes which can be submitted for review for the given study area and user.
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
   * Retrieve multiple pending changes at once.
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

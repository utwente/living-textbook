<?php

namespace App\Repository;

use App\Entity\StudyArea;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StudyAreaRepository extends ServiceEntityRepository
{
  const DEFAULT = 1;

  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, StudyArea::class);
  }

  /**
   * @return StudyArea|object
   */
  public function findDefault()
  {
    return $this->find(self::DEFAULT);
  }

  /**
   * @param User $owner
   *
   * @return mixed
   * @throws NonUniqueResultException
   */
  public function getOwnerAmount(User $owner){
    return $this->createQueryBuilder('sa')
        ->where('sa.owner = :owner')
        ->setParameter('owner', $owner)
        ->select('COUNT(sa.id)')
        ->getQuery()->getSingleScalarResult();
  }

  /**
   * Retrieve the visible study area's
   *
   * @param User $user
   *
   * @return StudyArea[]|Collection
   */
  public function getVisible(User $user)
  {
    return $this->getVisibleQueryBuilder($user)->getQuery()->getResult();
  }

  /**
   * Retrieve the first visible study area for the user
   *
   * @param User $user
   *
   * @return mixed|null
   */
  public function getFirstVisible(User $user){
    try {
      return $this->getVisibleQueryBuilder($user)->setMaxResults(1)->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException $e) {
      // Impossible due to max results
      return null;
    }
  }

  /**
   * Retrieve the amount of visible study area's
   *
   * @param User $user
   *
   * @return mixed
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function getVisibleCount(User $user)
  {
    return $this->getVisibleQueryBuilder($user)->select('COUNT(sa.id)')->getQuery()->getSingleScalarResult();
  }

  /**
   * Retrieve QueryBuilder for the visible study area's
   *
   * @param User $user
   *
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function getVisibleQueryBuilder(User $user)
  {
    $qb = $this->createQueryBuilder('sa');

    // @todo check for group rights
    return $qb
        ->where($qb->expr()->orX(
            $qb->expr()->eq('sa.owner', ':user'),
            $qb->expr()->eq('sa.accessType', ':public')
        ))
        ->setParameter('user', $user)
        ->setParameter('public', StudyArea::ACCESS_PUBLIC);
  }
}

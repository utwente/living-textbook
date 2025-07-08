<?php

namespace App\Repository;

use App\Entity\StudyArea;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @extends ServiceEntityRepository<StudyArea>
 */
class StudyAreaRepository extends ServiceEntityRepository
{
  public function __construct(
    ManagerRegistry $registry,
    private readonly AuthorizationCheckerInterface $authorizationChecker,
    private readonly TokenStorageInterface $tokenStorage)
  {
    parent::__construct($registry, StudyArea::class);
  }

  /** @throws NonUniqueResultException */
  public function getOwnerAmount(User $owner)
  {
    return $this->createQueryBuilder('sa')
      ->where('sa.owner = :owner')
      ->setParameter('owner', $owner)
      ->select('COUNT(sa.id)')
      ->getQuery()->getSingleScalarResult();
  }

  /**
   * Retrieve the visible study area's.
   *
   * @return StudyArea[]
   */
  public function getVisible(?User $user): array
  {
    return $this->getVisibleQueryBuilder($user)->getQuery()->getResult();
  }

  /**
   * Retrieve the first visible study area for the user.
   *
   * @return mixed|null
   */
  public function getFirstVisible(?User $user)
  {
    try {
      return $this->getVisibleQueryBuilder($user)->setMaxResults(1)->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException) {
      // Impossible due to max results
      return null;
    }
  }

  /**
   * Retrieve the amount of visible study area's.
   *
   * @throws NonUniqueResultException
   */
  public function getVisibleCount(User $user)
  {
    return $this->getVisibleQueryBuilder($user)->addSelect('COUNT(sa.id)')->getQuery()->getSingleScalarResult();
  }

  /**
   * Retrieve QueryBuilder for the visible study area's.
   *
   * @return QueryBuilder
   */
  public function getVisibleQueryBuilder(?User $user)
  {
    $qb = $this->createQueryBuilder('sa')
      ->leftJoin('sa.group', 'g')
      ->addSelect('g')
        // Special hidden select to move null groups to the end of the query result
        // https://stackoverflow.com/questions/12652034/how-can-i-order-by-null-in-dql
      ->addSelect('CASE WHEN g.name IS NULL THEN 1 ELSE 0 END HIDDEN _isFieldNull')
      ->orderBy('_isFieldNull', 'ASC')
      ->addOrderBy('g.name', 'ASC')
      ->addOrderBy('sa.name', 'ASC');

    // Return everything for super admins
    if ($this->tokenStorage->getToken() && $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
      return $qb;
    }

    // If user is not provided, only return open access study areas
    if (!$user) {
      return $qb
        ->where('sa.openAccess = :openAccess')
        ->setParameter('openAccess', true);
    }

    return $qb
      ->distinct()
      ->leftJoin('sa.userGroups', 'ug')
      ->leftJoin('ug.users', 'u')
      ->where($qb->expr()->orX(
        $qb->expr()->eq('sa.owner', ':user'),
        $qb->expr()->eq('sa.accessType', ':public'),
        $qb->expr()->eq('u', ':user')
      ))
      ->orWhere('sa.openAccess = :openAccess')
      ->setParameter('openAccess', true)
      ->setParameter('user', $user)
      ->setParameter('public', StudyArea::ACCESS_PUBLIC);
  }
}

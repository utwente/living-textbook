<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, User::class);
  }

  /**
   * Retrieve the super admins
   *
   * @return User[]
   */
  public function getSuperAdmins()
  {
    return $this->createQueryBuilder('u')
        ->where('u.isAdmin = :admin')
        ->setParameter('admin', true)
        ->getQuery()->getResult();
  }

  /**
   * Retrieve the available users for a user group
   *
   * @param UserGroup $userGroup
   *
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function getAvailableUsersForUserGroupQueryBuilder(UserGroup $userGroup)
  {
    $qb = $this->createQueryBuilder('u')
        ->orderBy('u.displayName', 'ASC');

    // Exclude users already in the group
    $userIds = $userGroup->getUsers()->map(function (User $user) {
      return $user->getId();
    });
    if (!$userIds->isEmpty()) {
      $qb->where('u.id NOT IN (:ids)')
          ->setParameter('ids', $userIds);
    }

    return $qb;
  }
}

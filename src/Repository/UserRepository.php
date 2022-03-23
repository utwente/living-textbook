<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class UserRepository.
 *
 * @method User|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method User|null findOneBy(array $criteria, array $orderBy = NULL)
 */
class UserRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, User::class);
  }

  /**
   * Retrieve fallback users.
   *
   * @return array
   */
  public function getFallbackUsers()
  {
    return $this->findBy(['isOidc' => false], ['username' => 'ASC']);
  }

  /**
   * Retrieve the super admins.
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
   * Find users for the given email addresses.
   *
   * @param array $emails Email addresses to search on
   *
   * @return User[]
   */
  public function getUsersForEmails(array $emails): array
  {
    $qb = $this->createQueryBuilder('u')
        ->where('u.username IN (:emails)')
        ->setParameter('emails', $emails);

    return $qb->getQuery()->getResult();
  }

  /**
   * Find a user for the given email address.
   *
   * @param $email
   */
  public function getUserForEmail($email): ?User
  {
    try {
      return $this->createQueryBuilder('u')
          ->where('u.username  = :email')
          ->setParameter('email', $email)
          ->setMaxResults(1)
          ->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException $e) {
      // Cannot happen
      return null;
    }
  }
}

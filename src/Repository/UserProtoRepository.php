<?php

namespace App\Repository;

use App\Entity\UserProto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class UserProtoRepository.
 *
 * @method UserProto|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method UserProto|null findOneBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class UserProtoRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, UserProto::class);
  }

  /** Find a user proto for the given email address. */
  public function getForEmail($email): ?UserProto
  {
    try {
      return $this->createQueryBuilder('u')
        ->where('u.email  = :email')
        ->setParameter('email', $email)
        ->setMaxResults(1)
        ->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException) {
      // Cannot happen
      return null;
    }
  }
}

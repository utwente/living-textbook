<?php

namespace App\Repository;

use App\Entity\UserGroupEmail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserGroupEmailRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, UserGroupEmail::class);
  }

  /** @return UserGroupEmail[] */
  public function findByEmail(string $email)
  {
    return $this->createQueryBuilder('uge')
        // Inner join to filter removed user groups
        ->innerJoin('uge.userGroup', 'ug')
        ->where('uge.email = :email')
        ->setParameter('email', $email)
        ->getQuery()->getResult();
  }
}

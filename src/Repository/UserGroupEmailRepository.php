<?php

namespace App\Repository;

use App\Entity\UserGroupEmail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserGroupEmailRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, UserGroupEmail::class);
  }

  /**
   * @param string $email
   *
   * @return UserGroupEmail[]
   */
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

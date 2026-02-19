<?php

namespace App\Repository;

use App\Entity\Help;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Help> */
class HelpRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Help::class);
  }

  /**
   * Retrieves the latest help page.
   *
   * @throws NoResultException
   * @throws NonUniqueResultException
   */
  public function getCurrent(): Help
  {
    return $this->createQueryBuilder('h')
      ->orderBy('h.createdAt', 'DESC')
      ->setMaxResults(1)
      ->getQuery()->getSingleResult();
  }
}

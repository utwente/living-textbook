<?php

namespace App\Repository;

use App\Entity\Help;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class HelpRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Help::class);
  }

  /**
   * Retrieves the latest help page
   *
   * @return Help
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

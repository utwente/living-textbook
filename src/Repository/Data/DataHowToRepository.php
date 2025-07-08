<?php

namespace App\Repository\Data;

use App\Entity\Data\DataHowTo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataHowTo>
 */
class DataHowToRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, DataHowTo::class);
  }
}

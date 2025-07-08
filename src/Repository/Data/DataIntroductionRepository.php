<?php

namespace App\Repository\Data;

use App\Entity\Data\DataIntroduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataIntroduction>
 */
class DataIntroductionRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, DataIntroduction::class);
  }
}

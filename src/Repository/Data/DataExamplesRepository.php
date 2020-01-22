<?php

namespace App\Repository\Data;

use App\Entity\Data\DataExamples;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DataExamplesRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, DataExamples::class);
  }
}

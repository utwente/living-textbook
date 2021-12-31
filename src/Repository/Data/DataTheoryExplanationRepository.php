<?php

namespace App\Repository\Data;

use App\Entity\Data\DataTheoryExplanation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DataTheoryExplanationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, DataTheoryExplanation::class);
  }
}

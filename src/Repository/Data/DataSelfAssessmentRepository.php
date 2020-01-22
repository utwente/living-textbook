<?php

namespace App\Repository\Data;

use App\Entity\Data\DataSelfAssessment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DataSelfAssessmentRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, DataSelfAssessment::class);
  }
}

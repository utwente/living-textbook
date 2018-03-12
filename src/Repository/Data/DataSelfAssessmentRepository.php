<?php

namespace App\Repository\Data;

use App\Entity\Data\DataSelfAssessment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DataSelfAssessmentRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, DataSelfAssessment::class);
  }
}

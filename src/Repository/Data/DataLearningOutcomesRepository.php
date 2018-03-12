<?php

namespace App\Repository\Data;

use App\Entity\Data\DataLearningOutcomes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DataLearningOutcomesRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, DataLearningOutcomes::class);
  }
}

<?php

namespace App\Repository;

use App\Entity\StylingConfigurationConceptOverride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StylingConfigurationConceptOverrideRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, StylingConfigurationConceptOverride::class);
  }
}

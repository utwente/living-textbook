<?php

namespace App\Repository;

use App\Entity\StylingConfigurationRelationOverride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StylingConfigurationRelationOverrideRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, StylingConfigurationRelationOverride::class);
  }
}

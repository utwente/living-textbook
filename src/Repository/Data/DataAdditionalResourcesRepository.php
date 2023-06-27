<?php

namespace App\Repository\Data;

use App\Entity\Data\DataAdditionalResources;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DataAdditionalResourcesRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, DataAdditionalResources::class);
  }
}

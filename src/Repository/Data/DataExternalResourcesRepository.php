<?php

namespace App\Repository\Data;

use App\Entity\Data\DataExternalResources;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DataExternalResourcesRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, DataExternalResources::class);
  }
}

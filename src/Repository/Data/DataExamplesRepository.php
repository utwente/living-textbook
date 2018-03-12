<?php

namespace App\Repository\Data;

use App\Entity\Data\DataExamples;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DataExamplesRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, DataExamples::class);
  }
}

<?php

namespace App\Repository\Data;

use App\Entity\Data\DataHowTo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DataHowToRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, DataHowTo::class);
  }
}

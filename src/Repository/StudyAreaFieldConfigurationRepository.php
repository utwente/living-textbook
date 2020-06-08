<?php

namespace App\Repository;

use App\Entity\StudyAreaFieldConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class StudyAreaFieldConfigurationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, StudyAreaFieldConfiguration::class);
  }
}

<?php

namespace App\Repository;

use App\Entity\StudyAreaFieldConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StudyAreaFieldConfiguration>
 */
class StudyAreaFieldConfigurationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, StudyAreaFieldConfiguration::class);
  }
}

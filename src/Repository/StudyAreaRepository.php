<?php

namespace App\Repository;

use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StudyAreaRepository extends ServiceEntityRepository
{
  const DEFAULT = 1;

  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, StudyArea::class);
  }

  /**
   * @return StudyArea|object
   */
  public function findDefault()
  {
    return $this->find(self::DEFAULT);
  }
}

<?php

namespace App\Repository;

use App\Entity\Concept;
use App\Entity\LayoutConfiguration;
use App\Entity\LayoutConfigurationOverride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<LayoutConfigurationOverride> */
class LayoutConfigurationOverrideRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, LayoutConfigurationOverride::class);
  }

  public function getUnique(Concept $concept, LayoutConfiguration $layoutConfiguration): ?LayoutConfigurationOverride
  {
    $qb = $this->getForLayoutConfigurationQb($layoutConfiguration);
    $qb = $qb->andWhere('l.concept = :concept')->setParameter('concept', $concept);

    return $qb->getQuery()->getOneOrNullResult();
  }

  public function getForLayoutConfigurationQb(LayoutConfiguration $layoutConfiguration): QueryBuilder
  {
    return $this->createQueryBuilder('l')
      ->where('l.layoutConfiguration = :layoutConfiguration')
      ->setParameter('layoutConfiguration', $layoutConfiguration);
  }
}

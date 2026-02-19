<?php

namespace App\Repository;

use App\Entity\ConceptRelation;
use App\Entity\StylingConfiguration;
use App\Entity\StylingConfigurationRelationOverride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<StylingConfigurationRelationOverride> */
class StylingConfigurationRelationOverrideRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, StylingConfigurationRelationOverride::class);
  }

  public function getUnique(ConceptRelation $relation, StylingConfiguration $stylingConfiguration): ?StylingConfigurationRelationOverride
  {
    $qb = $this->getForStylingConfigurationQb($stylingConfiguration);
    $qb = $qb->andWhere('s.relation = :relation')->setParameter('relation', $relation);

    return $qb->getQuery()->getOneOrNullResult();
  }

  public function getForStylingConfigurationQb(StylingConfiguration $stylingConfiguration): QueryBuilder
  {
    return $this->createQueryBuilder('s')
      ->where('s.stylingConfiguration = :stylingConfiguration')
      ->setParameter('stylingConfiguration', $stylingConfiguration);
  }
}

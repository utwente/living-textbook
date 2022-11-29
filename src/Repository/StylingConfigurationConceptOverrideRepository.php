<?php

namespace App\Repository;

use App\Entity\Concept;
use App\Entity\StylingConfiguration;
use App\Entity\StylingConfigurationConceptOverride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class StylingConfigurationConceptOverrideRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, StylingConfigurationConceptOverride::class);
  }

  public function getUnique(Concept $concept, StylingConfiguration $stylingConfiguration): ?StylingConfigurationConceptOverride
  {
    $qb = $this->getForStylingConfigurationQb($stylingConfiguration);
    $qb = $qb->andWhere('s.concept = :concept')->setParameter('concept', $concept);

    return $qb->getQuery()->getOneOrNullResult();
  }

  public function getForStylingConfigurationQb(StylingConfiguration $stylingConfiguration): QueryBuilder
  {
    return $this->createQueryBuilder('s')
        ->where('s.stylingConfiguration = :stylingConfiguration')
        ->setParameter('stylingConfiguration', $stylingConfiguration);
  }
}

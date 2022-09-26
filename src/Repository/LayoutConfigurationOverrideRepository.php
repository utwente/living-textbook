<?php

namespace App\Repository;

use App\Entity\Concept;
use App\Entity\LayoutConfiguration;
use App\Entity\LayoutConfigurationOverride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class LayoutConfigurationOverrideRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, LayoutConfigurationOverride::class);
  }

  public function findUnique(Concept $concept, LayoutConfiguration $layoutConfiguration): LayoutConfigurationOverride
  {
    $qb = $this->findForLayoutConfigurationQb($layoutConfiguration);
    $qb = $qb->andWhere('l.concept = :concept')->setParameter('concept', $concept);

    return $qb->getQuery()->getSingleResult();
  }

  public function findForConcept(Concept $concept): LayoutConfigurationOverride
  {
    $qb = $this->findForConceptQb($concept);
    return $qb->getQuery()->getSingleResult();
  }

  public function findForConceptQb(Concept $concept): QueryBuilder
  {
    return $this->createQueryBuilder('l')
        ->where('l.concept = :concept')
        ->setParameter('concept', $concept);
  }

  public function findForLayoutConfiguration(LayoutConfiguration $layoutConfiguration): LayoutConfigurationOverride
  {
    $qb = $this->findForLayoutConfigurationQb($layoutConfiguration);
    return $qb->getQuery()->getSingleResult();
  }

  public function findForLayoutConfigurationQb(LayoutConfiguration $layoutConfiguration): QueryBuilder
  {
    return $this->createQueryBuilder('l')
        ->where('l.layoutConfiguration = :layoutConfiguration')
        ->setParameter('layoutConfiguration', $layoutConfiguration);
  }

}

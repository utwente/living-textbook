<?php

namespace App\Repository;

use App\Entity\ConceptRelation;
use App\Entity\RelationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ConceptRelationRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, ConceptRelation::class);
  }

  /**
   * @param RelationType $relationType
   *
   * @return ConceptRelation[]|Collection
   */
  public function getByRelationType(RelationType $relationType)
  {
    return $this->createQueryBuilder('cr')
        ->join('cr.source', 'c')
        ->where('cr.relationType = :relationType')
        ->orderBy('c.name', 'ASC')
        ->setParameter('relationType', $relationType)
        ->getQuery()->getResult();
  }

  /**
   * Retrieve all links related to the given concepts
   *
   * @param array $concepts
   *
   * @return ConceptRelation[]|Collection
   */
  public function findByConcepts(array $concepts)
  {
    return $this->createQueryBuilder('l')
        ->distinct()
        ->where('l.source IN (:concepts)')
        ->orWhere('l.target IN (:concepts)')
        ->setParameter('concepts', $concepts)
        ->getQuery()->getResult();
  }
}

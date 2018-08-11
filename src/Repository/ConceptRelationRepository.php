<?php

namespace App\Repository;

use App\Entity\ConceptRelation;
use App\Entity\RelationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
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
    return $this->getByRelationTypeQb($relationType)
        ->getQuery()->getResult();
  }

  /**
   * @param RelationType $relationType
   *
   * @return int
   */
  public function getByRelationTypeCount(RelationType $relationType)
  {
    try {
      return $this->getByRelationTypeQb($relationType)
          ->select('COUNT(cr.id)')
          ->getQuery()->getSingleScalarResult();
    } catch (NonUniqueResultException $e) {
      return 0;
    }
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
    return $this->createQueryBuilder('cr')
        ->distinct()
        ->where('cr.source IN (:concepts)')
        ->orWhere('cr.target IN (:concepts)')
        ->setParameter('concepts', $concepts)
        ->getQuery()->getResult();
  }

  /**
   * @param RelationType $relationType
   *
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function getByRelationTypeQb(RelationType $relationType): \Doctrine\ORM\QueryBuilder
  {
    return $this->createQueryBuilder('cr')
        ->join('cr.source', 'c')
        ->where('cr.relationType = :relationType')
        ->orderBy('c.name', 'ASC')
        ->setParameter('relationType', $relationType);
  }
}

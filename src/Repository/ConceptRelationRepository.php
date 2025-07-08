<?php

namespace App\Repository;

use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\RelationType;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Drenso\Shared\Database\RepositoryTraits\FindIdsTrait;

/**
 * @extends ServiceEntityRepository<ConceptRelation>
 */
class ConceptRelationRepository extends ServiceEntityRepository
{
  use FindIdsTrait;

  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, ConceptRelation::class);
  }

  /** @return ConceptRelation[] */
  public function getByRelationType(RelationType $relationType): array
  {
    return $this->getByRelationTypeQb($relationType)
      ->getQuery()->getResult();
  }

  /**
   * @return int
   *
   * @noinspection PhpDocMissingThrowsInspection
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function getByRelationTypeCount(RelationType $relationType)
  {
    try {
      return $this->getByRelationTypeQb($relationType)
        ->select('COUNT(cr.id)')
        ->getQuery()->getSingleScalarResult();
    } catch (NonUniqueResultException) {
      return 0;
    }
  }

  /**
   * Retrieve all links related to the given concepts.
   *
   * @param Concept[] $concepts
   *
   * @return ConceptRelation[]
   */
  public function findByConcepts(array $concepts): array
  {
    return $this->createQueryBuilder('cr')
      ->distinct()
      ->where('cr.source IN (:concepts)')
      ->orWhere('cr.target IN (:concepts)')
      ->setParameter('concepts', $concepts)
      ->getQuery()->getResult();
  }

  public function getByRelationTypeQb(RelationType $relationType): QueryBuilder
  {
    return $this->createQueryBuilder('cr')
      ->join('cr.source', 'c')
      ->where('cr.relationType = :relationType')
      ->orderBy('c.name', 'ASC')
      ->setParameter('relationType', $relationType);
  }

  public function getByStudyAreaQb(StudyArea $studyArea): QueryBuilder
  {
    return $this->createQueryBuilder('cr')
      ->join('cr.source', 's')
      ->join('cr.target', 't')
      ->where('s.studyArea = :studyArea')
      ->andWhere('t.studyArea = :studyArea')
      ->setParameter('studyArea', $studyArea);
  }

  /** @return ConceptRelation[] */
  public function getByStudyArea(StudyArea $studyArea): array
  {
    return $this->getByStudyAreaQb($studyArea)
      ->getQuery()->getResult();
  }

  /**
   * @throws NonUniqueResultException
   *
   * @noinspection PhpDocMissingThrowsInspection
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function getCountForStudyArea(StudyArea $studyArea): int
  {
    return $this->getByStudyAreaQb($studyArea)
      ->select('COUNT(cr.id)')
      ->getQuery()->getSingleScalarResult();
  }
}

<?php

namespace App\Repository;

use App\Entity\RelationType;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

class RelationTypeRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, RelationType::class);
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return RelationType[]
   */
  public function findForStudyArea(StudyArea $studyArea): array
  {
    return $this->findForStudyAreaQb($studyArea)->getQuery()->getResult();
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return QueryBuilder
   */
  public function findForStudyAreaQb(StudyArea $studyArea): QueryBuilder
  {
    return $this->createQueryBuilder('rt')
        ->where('rt.studyArea = :studyArea')
        ->andWhere('rt.deletedAt IS NULL')
        ->orderBy('rt.name', 'ASC')
        ->setParameter('studyArea', $studyArea);
  }

  /**
   * Get or create the relation type with the given name in the specified study area
   *
   * @param StudyArea $studyArea
   * @param string    $name
   *
   * @return RelationType
   *
   * @noinspection PhpUnhandledExceptionInspection
   * @noinspection PhpDocMissingThrowsInspection
   */
  public function getOrCreateRelation(StudyArea $studyArea, string $name): RelationType
  {
    try {
      $foundRelation = $this->findForStudyAreaQb($studyArea)
          ->andWhere('rt.name = :name')
          ->setParameter('name', $name)
          ->setMaxResults(1)
          ->getQuery()->getSingleResult();
    } catch (NoResultException $e) {
      $foundRelation = (new RelationType())
          ->setStudyArea($studyArea)
          ->setName($name);
      $this->getEntityManager()->persist($foundRelation);
      $this->getEntityManager()->flush($foundRelation);
    }

    return $foundRelation;
  }
}

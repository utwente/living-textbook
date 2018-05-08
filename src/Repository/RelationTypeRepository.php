<?php

namespace App\Repository;

use App\Entity\RelationType;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RelationTypeRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, RelationType::class);
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return RelationType[]|Collection
   */
  public function findForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)->getQuery()->getResult();
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function findForStudyAreaQb(StudyArea $studyArea)
  {
    return $this->createQueryBuilder('rt')
        ->where('rt.studyArea = :studyArea')
        ->andWhere('rt.deletedAt IS NULL')
        ->orderBy('rt.name', 'ASC')
        ->setParameter('studyArea', $studyArea);
  }
}

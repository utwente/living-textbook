<?php

namespace App\Repository;

use App\Entity\StudyArea;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ConceptRepository extends EntityRepository
{

  /**
   * @return array
   */
  public function findAllOrderedByName()
  {
    $qb = $this->createQueryBuilder('c')
        ->orderBy('c.name', 'ASC');

    $this->loadRelations($qb, 'c');

    return $qb->getQuery()->getResult();
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return array
   */
  public function findByStudyAreaOrderedByName(StudyArea $studyArea)
  {
    $qb = $this->createQueryBuilder('c')
        ->join('c.studyAreas', 'csa')
        ->join('csa.studyArea', 'sa')
        ->where('sa = :studyArea')
        ->setParameter(':studyArea', $studyArea)
        ->orderBy('c.name', 'asc');

    $this->loadRelations($qb, 'c');

    return $qb->getQuery()->getResult();
  }

  /**
   * Eagerly load the concept relations, while applying the soft deletable filter
   *
   * @param QueryBuilder $qb
   * @param string       $alias
   */
  private function loadRelations(QueryBuilder &$qb, string $alias)
  {
    $qb
        ->leftJoin($alias . '.outgoingRelations', 'r')
        ->leftJoin($alias . '.incomingRelations', 'ir')
        ->addSelect('r')
        ->addSelect('ir');
  }
}

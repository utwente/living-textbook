<?php

namespace App\Repository;

use App\Entity\StudyArea;
use Doctrine\ORM\EntityRepository;

class ConceptRepository extends EntityRepository
{

  /**
   * @return array
   */
  public function findAllOrderedByName()
  {
    return $this->findBy([], ['name' => 'ASC']);
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
    return $qb->getQuery()->getResult();
  }
}

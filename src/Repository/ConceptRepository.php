<?php

namespace App\Repository;

use App\Entity\Concept;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ConceptRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, Concept::class);
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return QueryBuilder
   */
  public function findForStudyAreaOrderByNameQb(StudyArea $studyArea): QueryBuilder
  {
    return $this->createQueryBuilder('c')
        ->where('c.studyArea = :studyArea')
        ->setParameter(':studyArea', $studyArea)
        ->orderBy('c.name', 'asc');
  }

  /**
   * @param StudyArea $studyArea
   * @param bool      $preLoadData
   *
   * @return array
   */
  public function findForStudyAreaOrderedByName(StudyArea $studyArea, bool $preLoadData = false)
  {
    $qb = $this->findForStudyAreaOrderByNameQb($studyArea);

    $this->loadRelations($qb, 'c');

    if ($preLoadData) {
      $this->preLoadData($qb, 'c');
    }

    return $qb->getQuery()->getResult();
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return mixed
   * @throws NonUniqueResultException
   */
  public function getCountForStudyArea(StudyArea $studyArea)
  {
    return $this->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->where('c.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->getQuery()->getSingleScalarResult();
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

  /**
   * Eagerly load the text data
   *
   * @param QueryBuilder $qb
   * @param string       $alias
   */
  private function preLoadData(QueryBuilder &$qb, string $alias)
  {
    $qb
        ->join($alias . '.examples', 'de')
        ->join($alias . '.introduction', 'di')
        ->join($alias . '.theoryExplanation', 'dt')
        ->join($alias . '.howTo', 'dh')
        ->join($alias . '.selfAssessment', 'ds')
        ->addSelect('de')
        ->addSelect('di')
        ->addSelect('dt')
        ->addSelect('dh')
        ->addSelect('ds');
  }
}

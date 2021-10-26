<?php

namespace App\Repository;

use App\Entity\StudyArea;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class TagRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Tag::class);
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return Tag[]
   */
  public function findForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
      ->getQuery()->getResult();
  }


  /**
   * @param StudyArea $studyArea
   *
   * @return mixed
   *
   * @noinspection PhpDocMissingThrowsInspection
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function getCountForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
      ->select('COUNT(t.id)')
      ->getQuery()->getSingleScalarResult();
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return QueryBuilder
   */
  public function findForStudyAreaQb(StudyArea $studyArea): QueryBuilder
  {
    return $this->createQueryBuilder('t')
      ->where('t.studyArea = :studyArea')
      ->setParameter('studyArea', $studyArea)
      ->orderBy('t.name', 'ASC');
  }
}

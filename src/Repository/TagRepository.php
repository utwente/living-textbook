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

  /** @return Tag[] */
  public function findForStudyArea(StudyArea $studyArea, ?array $ids = null): array
  {
    $qb = $this->findForStudyAreaQb($studyArea);

    if ($ids !== null) {
      $qb->andWhere($qb->expr()->in('t.id', ':ids'))
        ->setParameter('ids', $ids);
    }

    return $qb->getQuery()->getResult();
  }

  /**
   * @noinspection PhpDocMissingThrowsInspection
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function getCountForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
      ->select('COUNT(t.id)')
      ->getQuery()->getSingleScalarResult();
  }

  public function findForStudyAreaQb(StudyArea $studyArea): QueryBuilder
  {
    return $this->createQueryBuilder('t')
      ->where('t.studyArea = :studyArea')
      ->setParameter('studyArea', $studyArea)
      ->orderBy('t.name', 'ASC');
  }
}

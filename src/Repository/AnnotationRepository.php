<?php

namespace App\Repository;

use App\Entity\Annotation;
use App\Entity\Concept;
use App\Entity\StudyArea;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class AnnotationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Annotation::class);
  }

  public function getForUserAndStudyArea(User $user, StudyArea $studyArea)
  {
    // Default query part
    $qb = $this->createQueryBuilder('a');

    return $qb
      ->leftJoin('a.concept', 'c')
      ->where('c.studyArea = :studyArea')
      ->andWhere($this->getVisibilityWhere($qb, $user, $studyArea))
      ->setParameter('studyArea', $studyArea)
      ->orderBy('a.context')
      ->addOrderBy('a.start')
      ->select('a')
      ->getQuery()->getResult();
  }

  /**
   * Find by user and concept.
   *
   * @return Annotation[]
   */
  public function getForUserAndConcept(User $user, Concept $concept)
  {
    // Default query part
    $qb = $this->createQueryBuilder('a');

    return $qb
      ->where('a.concept = :concept')
      ->andWhere($this->getVisibilityWhere($qb, $user, $concept->getStudyArea()))
      ->setParameter('concept', $concept)
      ->orderBy('a.context')
      ->addOrderBy('a.start')
      ->getQuery()->getResult();
  }

  /** Get visibility annotation count for a user in a study area. */
  public function getCountsForUserInStudyArea(User $user, StudyArea $studyArea): array
  {
    // Concepts query part
    $conceptsQuery = $this->getEntityManager()
      ->createQueryBuilder()
      ->select('c.id')
      ->from(Concept::class, 'c')
      ->where('c.studyArea = :studyArea');

    // Default query part
    $qb = $this->createQueryBuilder('a');

    $counts = $qb
      ->select('IDENTITY(a.concept), count(a.id)')
      ->where($qb->expr()->in('a.concept', $conceptsQuery->getDQL()))
      ->andWhere($this->getVisibilityWhere($qb, $user, $studyArea))
      ->setParameter('studyArea', $studyArea)
      ->groupBy('a.concept')
      ->getQuery()->getResult();

    // Map result to id => count array
    $result = [];
    array_walk($counts, function ($value) use (&$result) {
      $result[$value[1]] = $value[2];
    });

    return $result;
  }

  private function getVisibilityWhere(QueryBuilder $qb, User $user, StudyArea $studyArea): Orx
  {
    $qb->setParameter('everybody', Annotation::everybodyVisibility())
      ->setParameter('isTeacher', $studyArea->isEditable($user))
      ->setParameter('teacher', Annotation::teacherVisibility())
      ->setParameter('user', $user);

    return $qb->expr()->orX(
      $qb->expr()->eq('a.visibility', ':everybody'),
      $qb->expr()->andX(
        $qb->expr()->eq(true, ':isTeacher'),
        $qb->expr()->eq('a.visibility', ':teacher')
      ),
      $qb->expr()->eq('a.user', ':user')
    );
  }
}

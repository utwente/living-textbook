<?php

namespace App\Repository;

use App\Entity\Annotation;
use App\Entity\Concept;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class AnnotationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Annotation::class);
  }

  /**
   * Find by user and concept
   *
   * @param User    $user
   * @param bool    $isTeacher
   * @param Concept $concept
   *
   * @return Annotation[]
   */
  public function getForUserAndConcept(User $user, bool $isTeacher, Concept $concept)
  {
    // Default query part
    $qb = $this->createQueryBuilder('a');

    return $qb
        ->where('a.concept = :concept')
        ->andWhere($qb->expr()->orX(
            $qb->expr()->eq('a.visibility', ':everybody'),
            $qb->expr()->andX(
                $qb->expr()->eq(true, ':isTeacher'),
                $qb->expr()->eq('a.visibility', ':teacher')
            ),
            $qb->expr()->eq('a.user', ':user')
        ))
        ->setParameter('concept', $concept)
        ->setParameter('everybody', Annotation::everybodyVisibility())
        ->setParameter('isTeacher', $isTeacher)
        ->setParameter('teacher', Annotation::teacherVisibility())
        ->setParameter('user', $user)
        ->orderBy('a.context')
        ->addOrderBy('a.start')
        ->getQuery()->getResult();
  }

}

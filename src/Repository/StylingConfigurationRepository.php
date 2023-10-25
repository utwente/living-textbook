<?php

namespace App\Repository;

use App\Entity\StudyArea;
use App\Entity\StylingConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class StylingConfigurationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, StylingConfiguration::class);
  }

  public function findForStudyArea(StudyArea $studyArea, int $id): ?StylingConfiguration
  {
    $qb = $this->findForStudyAreaQb($studyArea);

    $qb = $qb->andWhere('t.id = :id')->setParameter('id', $id);

    return $qb->getQuery()->getOneOrNullResult();
  }

  public function findForStudyAreaQb(StudyArea $studyArea): QueryBuilder
  {
    return $this->createQueryBuilder('t')
      ->where('t.studyArea = :studyArea')
      ->setParameter('studyArea', $studyArea);
  }
}

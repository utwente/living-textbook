<?php

namespace App\Repository;

use App\Entity\ExternalResource;
use App\Entity\StudyArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ExternalResourceRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, ExternalResource::class);
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return ExternalResource[]
   */
  public function findForStudyArea(StudyArea $studyArea)
  {
    return $this->findForStudyAreaQb($studyArea)
        ->getQuery()->getResult();
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function findForStudyAreaQb(StudyArea $studyArea): \Doctrine\ORM\QueryBuilder
  {
    return $this->createQueryBuilder('er')
        ->where('er.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea)
        ->orderBy('er.title', 'ASC');
  }
}

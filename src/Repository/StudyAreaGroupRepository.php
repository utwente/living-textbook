<?php

namespace App\Repository;

use App\Entity\StudyAreaGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Override;

/**
 * @extends ServiceEntityRepository<StudyAreaGroup>
 */
class StudyAreaGroupRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, StudyAreaGroup::class);
  }

  /** @return StudyAreaGroup[] */
  #[Override]
  public function findAll()
  {
    return $this->findBy([], ['name' => 'ASC']);
  }
}

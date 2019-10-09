<?php

namespace App\Repository;

use App\Entity\StudyAreaGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StudyAreaGroup|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method StudyAreaGroup|null findOneBy(array $criteria, array $orderBy = NULL)
 * @method StudyAreaGroup[]    findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class StudyAreaGroupRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, StudyAreaGroup::class);
  }

  /**
   * @return StudyAreaGroup[]
   */
  public function findAll()
  {
    return $this->findBy([], ['name' => 'ASC']);
  }

}

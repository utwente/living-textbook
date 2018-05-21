<?php

namespace App\Repository;

use App\Entity\StudyArea;
use App\Entity\UserGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserGroupRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, UserGroup::class);
  }

  /**
   * Retrieve the user group for a study area by type
   *
   * @param StudyArea $studyArea
   * @param string    $groupType
   *
   * @return UserGroup
   * @throws \InvalidArgumentException
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function getForType(StudyArea $studyArea, string $groupType)
  {
    if (!in_array($groupType, UserGroup::getGroupTypes())) {
      throw new \InvalidArgumentException(sprintf('Access type "%s" does not exist!', $groupType));
    }

    try {
      return $this->createQueryBuilder('ug')
          ->where('ug.studyArea = :studyArea')
          ->andWhere('ug.groupType = :type')
          ->setParameter('studyArea', $studyArea)
          ->setParameter('type', $groupType)
          ->getQuery()->getSingleResult();
    } catch (NoResultException $e) {
      return NULL;
    }
  }
}

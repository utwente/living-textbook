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

  /**
   * Remove obsolete user groups based on the new study area access type
   * Note that flush is required after this function call!
   *
   * @param StudyArea $studyArea
   */
  public function removeObsoleteGroups(StudyArea $studyArea)
  {
    $qb = $this->createQueryBuilder('ug')
        ->where('ug.studyArea = :studyArea')
        ->setParameter('studyArea', $studyArea);

    switch ($studyArea->getAccessType()) {
      case StudyArea::ACCESS_INDIVIDUAL:
        // Remove all groups
        break;
      case StudyArea::ACCESS_GROUP:
        // Nothing to do
        return;
      case StudyArea::ACCESS_PUBLIC:
        // Remove viewer group
        $qb->andWhere('ug.groupType = :groupType')
            ->setParameter('groupType', UserGroup::GROUP_VIEWER);
        break;
    }

    // Execute the query
    $groups = $qb->getQuery()->getResult();

    // Remove with entity manager to trigger soft delete
    array_walk($groups, function ($group) {
      $this->getEntityManager()->remove($group);
    });
  }
}

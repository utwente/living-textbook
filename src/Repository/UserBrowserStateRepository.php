<?php

namespace App\Repository;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Entity\UserBrowserState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserBrowserState>
 */
class UserBrowserStateRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, UserBrowserState::class);
  }

  public function findForUser(User $user, StudyArea $studyArea): ?UserBrowserState
  {
    return $this->findOneBy(['user' => $user, 'studyArea' => $studyArea]);
  }
}

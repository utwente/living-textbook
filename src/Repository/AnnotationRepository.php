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
   * @param Concept $concept
   *
   * @return array
   */
  public function getForUserAndConcept(User $user, Concept $concept)
  {
    return $this->findBy([
        'user'    => $user,
        'concept' => $concept,
    ], [
        'context' => 'ASC',
        'start'   => 'ASC',
    ]);
  }

}

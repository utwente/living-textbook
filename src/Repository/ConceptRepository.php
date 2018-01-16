<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ConceptRepository extends EntityRepository
{

  /**
   * @return array
   */
  public function findAllOrderedByName()
  {
    return $this->findBy([], ['name' => 'ASC']);
  }
}

<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class NodeRepository extends EntityRepository
{

  /**
   * @return array
   */
  public function findAllOrderedByName()
  {
    return $this->findBy([], ['name' => 'ASC']);
  }
}

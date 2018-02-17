<?php

namespace App\Repository;

use App\Entity\StudyArea;
use Doctrine\ORM\EntityRepository;

class StudyAreaRepository extends EntityRepository
{
  const DEFAULT = 1;

  /**
   * @return StudyArea|object
   */
  public function findDefault()
  {
    return $this->find(self::DEFAULT);
  }
}

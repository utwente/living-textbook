<?php

namespace App\Review\Exception;

use App\Entity\PendingChange;
use Exception;

class IncompatibleChangeMergeException extends Exception
{
  /**
   * IncompatibleChangeMergeException constructor.
   *
   * @param PendingChange $original
   * @param PendingChange $merge
   */
  public function __construct(PendingChange $original, PendingChange $merge)
  {
    parent::__construct(sprintf('The pending change (id: %d, type: %s, objectId: %d) is cannot be merged with this one (id: %d, type: %s, objectId: %d)',
        $original->getId(), $original->getObjectType(), $original->getObjectId(), $original->getId(), $original->getObjectType(), $original->getObjectType()));
  }
}

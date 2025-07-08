<?php

namespace App\Review\Exception;

use App\Entity\PendingChange;
use Exception;

use function sprintf;

class IncompatibleChangeMergeException extends Exception
{
  /** IncompatibleChangeMergeException constructor. */
  public function __construct(PendingChange $original, PendingChange $merge)
  {
    parent::__construct(sprintf('The pending change (id: %d, type: %s, objectId: %d) is cannot be merged with this one (id: %d, type: %s, objectId: %d)',
      $original->getId(), $original->getObjectType(), $original->getObjectId(), $original->getId(), $original->getObjectType(), $original->getObjectType()));
  }
}

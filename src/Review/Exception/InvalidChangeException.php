<?php

namespace App\Review\Exception;

use App\Entity\PendingChange;
use Exception;

use function sprintf;

class InvalidChangeException extends Exception
{
  public function __construct(PendingChange $pendingChange)
  {
    parent::__construct(sprintf(
      'Related object %s with id %d could not be found for pending change with it %d.',
      $pendingChange->getObjectType(),
      $pendingChange->getObjectId(),
      $pendingChange->getId()
    ));
  }
}

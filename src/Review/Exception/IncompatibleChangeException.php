<?php

namespace App\Review\Exception;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\PendingChange;
use App\Entity\Traits\ReviewableTrait;
use Exception;

class IncompatibleChangeException extends Exception
{
  /**
   * IncompatibleChangeException constructor.
   *
   * @param ReviewableTrait|ReviewableInterface $reviewable
   */
  public function __construct($reviewable, PendingChange $pendingChange)
  {
    parent::__construct(sprintf('The pending change (id: %d, type: %s, objectId: %d) is not compatible with this object (id: %d, type: %s)',
      $pendingChange->getId(), $pendingChange->getObjectType(), $pendingChange->getObjectId(), $reviewable->getId(), $reviewable->getReviewName()));
  }
}

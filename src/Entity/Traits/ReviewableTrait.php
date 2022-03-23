<?php

namespace App\Entity\Traits;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\PendingChange;
use App\Review\Exception\IncompatibleChangeException;

/**
 * Trait ReviewableTrait
 * Some default function for reviewable entities.
 */
trait ReviewableTrait
{
  /** Returns the review name. */
  public function getReviewName(): string
  {
    return self::class;
  }

  /**
   * Test whether the supplied change belongs to this entity.
   *
   * @throws IncompatibleChangeException
   */
  public function testChange(PendingChange $change): ReviewableInterface
  {
    if ($change->getObjectType() !== $this->getReviewName()
        || $change->getObjectId() !== $this->getId()) {
      throw new IncompatibleChangeException($this, $change);
    }

    return $change->getObject();
  }
}

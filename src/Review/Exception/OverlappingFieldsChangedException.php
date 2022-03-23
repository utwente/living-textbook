<?php

namespace App\Review\Exception;

use App\Entity\PendingChange;
use Exception;

/**
 * Class OverlappingFieldsChangedException.
 *
 * Thrown when the pending changes can not be merged due to overlapping field changes
 */
class OverlappingFieldsChangedException extends Exception
{
  public function __construct(PendingChange $change1, PendingChange $change2)
  {
    parent::__construct(sprintf(
        'The changed properties of the supplied pending changes overlap! Overlapping properties: %s',
        implode(', ', array_intersect($change1->getChangedFields(), $change2->getChangedFields()))
    ));
  }
}

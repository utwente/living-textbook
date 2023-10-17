<?php

namespace App\Review\Exception;

use App\Entity\Contracts\ReviewableInterface;
use Exception;

class IncompatibleFieldChangedException extends Exception
{
  /** IncompatibleFieldChangedException constructor. */
  public function __construct(ReviewableInterface $object, string $changedField)
  {
    parent::__construct(sprintf('The changed field "%s" is not compatible with the object "%s"',
      $changedField, $object->getReviewName()));
  }
}

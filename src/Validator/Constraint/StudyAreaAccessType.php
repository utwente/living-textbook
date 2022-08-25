<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Class StudyAreaAccessType.
 *
 * @Annotation
 */
class StudyAreaAccessType extends Constraint
{
  /** Sets this validator as class validator. */
  public function getTargets(): array|string
  {
    return self::PROPERTY_CONSTRAINT;
  }
}

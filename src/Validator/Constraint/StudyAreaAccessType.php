<?php

namespace App\Validator\Constraint;

use Attribute;
use Override;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
class StudyAreaAccessType extends Constraint
{
  /** Sets this validator as class validator. */
  #[Override]
  public function getTargets(): string
  {
    return self::PROPERTY_CONSTRAINT;
  }
}

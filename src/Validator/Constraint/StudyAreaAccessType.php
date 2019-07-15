<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Class StudyAreaAccessType
 *
 * @Annotation
 */
class StudyAreaAccessType extends Constraint
{

  /**
   * Sets this validator as class validator
   *
   * @return array|string
   */
  public function getTargets()
  {
    return self::PROPERTY_CONSTRAINT;
  }
}

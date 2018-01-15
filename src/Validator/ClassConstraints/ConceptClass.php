<?php

namespace App\Validator\ClassConstraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class ConceptClass
 *
 * @author Tobias
 *
 * @Annotation
 */
class ConceptClass extends Constraint
{
  public $noStudyAreaMessage = 'concept.no-study-area-given';

  public function getTargets()
  {
    return self::CLASS_CONSTRAINT;
  }

}
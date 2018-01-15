<?php

namespace App\Validator\ClassConstraints;

use App\Entity\Concept;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ConceptClassValidator
 *
 * @author Tobias
 */
class ConceptClassValidator extends ConstraintValidator
{

  /**
   * Make sure a concept has at least one studyarea before being able to save it.
   * @param Concept                 $value
   * @param ConceptClass|Constraint $constraint
   */
  public function validate($value, Constraint $constraint)
  {
    if (count($value->getStudyAreas()) < 1) {
      $this->context
          ->buildViolation($constraint->noStudyAreaMessage)
          ->addViolation();
    }
  }

}
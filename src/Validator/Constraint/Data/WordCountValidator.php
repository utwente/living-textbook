<?php

namespace App\Validator\Constraint\Data;

use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class WordCountValidator.
 *
 * @author BobV
 */
class WordCountValidator extends ConstraintValidator
{
  /**
   * Checks if the passed value is valid.
   *
   * @param mixed      $value      The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  #[Override]
  public function validate($value, Constraint $constraint)
  {
    // Check constraint
    if (!($constraint instanceof WordCount)) {
      throw new UnexpectedTypeException($constraint, WordCount::class);
    }

    // Count words
    $count = str_word_count(strip_tags((string)$value));

    // Check count
    $message = false;
    if ($count < $constraint->min) {
      $message = $constraint->minMessage;
    } elseif ($count > $constraint->max) {
      $message = $constraint->maxMessage;
    }

    // Check whether violation is detected
    if (!$message) {
      return;
    }

    // Build violation
    $this->context->buildViolation($message, [
      '%min%'   => $constraint->min,
      '%max%'   => $constraint->max,
      '%count%' => $count,
    ])->addViolation();
  }
}

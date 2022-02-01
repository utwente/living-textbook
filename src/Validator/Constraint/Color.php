<?php

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraints\Regex;

/**
 * @Annotation
 */
class Color extends Regex
{
  public function __construct($options = null)
  {
    $this->message = 'color.invalid';
    $this->pattern = '/^#([0-9A-F]{3}){1,2}$/';

    parent::__construct($options);
  }

  public function getRequiredOptions(): array
  {
    return [];
  }

  public function validatedBy(): string
  {
    return Regex::class . 'Validator';
  }
}

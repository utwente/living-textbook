<?php

namespace App\Validator\Constraint;

use Attribute;
use Override;
use Symfony\Component\Validator\Constraints\Regex;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Color extends Regex
{
  public function __construct($options = null)
  {
    $this->message = 'color.invalid';
    $this->pattern = '/^#([0-9A-F]{3}){1,2}$/i';

    parent::__construct($options);
  }

  #[Override]
  public function getRequiredOptions(): array
  {
    return [];
  }

  #[Override]
  public function validatedBy(): string
  {
    return Regex::class . 'Validator';
  }
}

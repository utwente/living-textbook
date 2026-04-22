<?php

namespace App\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\Regex;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Color extends Compound
{
  protected function getConstraints(array $options): array
  {
    return [
      new Regex(
        pattern: '/^#([0-9A-F]{3}){1,2}$/i',
        message: 'color.invalid',
      ),
    ];
  }
}

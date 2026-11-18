<?php

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class NotFoundWhenLearningOutcomesDisabled
{
  public function __construct(public string $subject)
  {
  }
}

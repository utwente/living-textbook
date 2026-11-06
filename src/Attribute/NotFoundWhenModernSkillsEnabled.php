<?php

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class NotFoundWhenModernSkillsEnabled
{
  public function __construct(public string $subject)
  {
  }
}

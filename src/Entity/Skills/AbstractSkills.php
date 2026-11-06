<?php

namespace App\Entity\Skills;

use Drenso\Shared\Helper\ArrayHelper;

abstract class AbstractSkills implements SkillsInterface
{
  public function hasData(): bool
  {
    return !empty(ArrayHelper::filterEmptyValuesFromStringArray($this->getSkills()));
  }

  /** @return array<string|null> */
  abstract public function getSkills(): array;
}

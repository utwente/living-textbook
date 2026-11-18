<?php

namespace App\Entity\Skills;

use Drenso\Shared\Helper\ArrayHelper;

use function count;

abstract class AbstractSkills implements SkillsInterface
{
  public function hasData(): bool
  {
    return !empty($this->skillsWithValue());
  }

  public function skillCount(): int
  {
    return count($this->skillsWithValue());
  }

  /** @return array<string|null> */
  abstract public function getSkills(): array;

  /** @return string[] */
  public function skillsWithValue(): array
  {
    return ArrayHelper::filterEmptyValuesFromStringArray(ArrayHelper::filterNullValuesFromArray($this->getSkills()));
  }
}

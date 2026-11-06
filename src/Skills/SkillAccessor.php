<?php

namespace App\Skills;

use App\Entity\Data\DataSkills;
use App\Entity\Skills\SkillSetEnum;
use App\Entity\Skills\SkillsEnumInterface;
use App\Entity\Skills\SkillsInterface;
use Drenso\Shared\Helper\StringHelper;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final readonly class SkillAccessor
{
  public function __construct(private PropertyAccessorInterface $propertyAccessor) {
  }

  public function setValue(DataSkills $skillData, SkillsEnumInterface $skill, ?string $value): void
  {
    $skillData = $this->getSkillSet($skillData, $skill->getSkillSet());

    $this->propertyAccessor->setValue($skillData, $this->propertyName($skill), StringHelper::emptyToNull($value));
  }

  public function getValue(DataSkills $skillData, SkillsEnumInterface $skill): ?string
  {
    $skillData = $this->getSkillSet($skillData, $skill->getSkillSet());

    return $this->propertyAccessor->getValue($skillData, $this->propertyName($skill));
  }

  public function propertyName(SkillSetEnum|SkillsEnumInterface $skill): string
  {
    return strtolower($skill->name);
  }

  private function getSkillSet(DataSkills $skillData, SkillSetEnum $skillLevel): SkillsInterface
  {
    return $this->propertyAccessor->getValue($skillData, $this->propertyName($skillLevel));
  }
}

<?php

namespace App\Skills;

use App\Entity\Data\DataSkills;
use App\Entity\Skills\SkillsEnumInterface;
use App\Entity\Skills\SkillSetEnum;
use App\Entity\Skills\SkillsInterface;
use Drenso\Shared\Helper\StringHelper;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function array_combine;
use function array_filter;
use function array_map;
use function strtolower;

final readonly class SkillAccessor
{
  public function __construct(private PropertyAccessorInterface $propertyAccessor)
  {
  }

  /** @return array<string, array<string, string|null>> */
  public function getAsArrayData(DataSkills $skillData, ?string $staticValue = null): array
  {
    $data = [];
    foreach (SkillSetEnum::cases() as $skillSet) {
      $skills = [];
      foreach ($skillSet->getSkills() as $skill) {
        $skills[$this->propertyName($skill)] = $staticValue ?? $this->getValue($skillData, $skill);
      }

      $data[$this->propertyName($skillSet)] = $skills;
    }

    return $data;
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

  public function getSkillValues(DataSkills $skillData, SkillSetEnum $skillSet): array
  {
    return array_filter(
      array_combine(
        array_map(
          fn (SkillsEnumInterface $skill): string => $this->propertyName($skill),
          $skillSet->getSkills(),
        ),
        array_map(
          fn (SkillsEnumInterface $skill): ?string => $this->getValue($skillData, $skill),
          $skillSet->getSkills(),
        )
      ),
      static fn (?string $value): bool => (bool)$value
    );
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

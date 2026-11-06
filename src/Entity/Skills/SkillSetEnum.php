<?php

namespace App\Entity\Skills;

enum SkillSetEnum: int
{
  case REMEMBER   = 1;
  case UNDERSTAND = 2;
  case APPLY      = 3;
  case ANALYSE    = 4;
  case EVALUATE   = 5;
  case CREATE     = 6;

  /** @return SkillsEnumInterface[] */
  public function getSkills(): array
  {
    return match ($this) {
      self::REMEMBER   => RememberSkillsEnum::cases(),
      self::UNDERSTAND => UnderstandSkillsEnum::cases(),
      self::APPLY      => ApplySkillsEnum::cases(),
      self::ANALYSE    => AnalyseSkillsEnum::cases(),
      self::EVALUATE   => EvaluateSkillsEnum::cases(),
      self::CREATE     => CreateSkillsEnum::cases(),
    };
  }
}

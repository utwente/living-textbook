<?php

namespace App\Entity\Skills;

enum AnalyseSkillsEnum: int implements SkillsEnumInterface
{
  use SkillTranslationTrait;

  case ATTRIBUTING     = 1;
  case DIFFERENTIATING = 2;
  case ORGANISING      = 3;

  public function getSkillSet(): SkillSetEnum
  {
    return SkillSetEnum::ANALYSE;
  }
}

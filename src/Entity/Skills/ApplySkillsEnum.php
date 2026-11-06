<?php

namespace App\Entity\Skills;

enum ApplySkillsEnum: int implements SkillsEnumInterface
{
  use SkillTranslationTrait;

  case EXECUTING    = 1;
  case IMPLEMENTING = 2;

  public function getSkillSet(): SkillSetEnum
  {
    return SkillSetEnum::APPLY;
  }
}

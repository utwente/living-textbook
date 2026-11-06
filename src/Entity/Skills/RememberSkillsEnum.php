<?php

namespace App\Entity\Skills;

enum RememberSkillsEnum: int implements SkillsEnumInterface
{
  use SkillTranslationTrait;

  case RECALLING   = 1;
  case RECOGNISING = 2;

  public function getSkillSet(): SkillSetEnum
  {
    return SkillSetEnum::REMEMBER;
  }
}

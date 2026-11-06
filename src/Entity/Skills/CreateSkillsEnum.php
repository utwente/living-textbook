<?php

namespace App\Entity\Skills;

enum CreateSkillsEnum: int implements SkillsEnumInterface
{
  case GENERATING = 1;
  case PLANNING   = 2;
  case PRODUCING  = 3;

  public function getSkillSet(): SkillSetEnum
  {
    return SkillSetEnum::CREATE;
  }
}

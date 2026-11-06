<?php

namespace App\Entity\Skills;

enum EvaluateSkillsEnum: int implements SkillsEnumInterface
{
  case CHECKING   = 1;
  case CRITIQUING = 2;

  public function getSkillSet(): SkillSetEnum
  {
    return SkillSetEnum::EVALUATE;
  }
}
